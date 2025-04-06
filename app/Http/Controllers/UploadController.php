<?php

namespace App\Http\Controllers;

use App\Jobs\InitChunks;
use App\Models\Row;
use App\Processors\XlsProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use function PHPUnit\Framework\isNull;

class UploadController extends Controller
{

    public function index()
    {
        return view('upload.index');
    }

    public function upload(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx|max:4096',
        ]);

        if(!$request->file('file')->isValid()) {
            return back()->withErrors('File upload failed.');
        }

        $path = $request->file('file')->store('uploads');

        try {
            IOFactory::load(storage_path('app/private').'/'.$path);

        } catch(ReaderException $e) {
            return back()->withErrors('Unable to read the file. Please, open it in official MS Excel, press ctrl+S and try again');
        }
        catch (\Throwable $e) {
            return back()->withErrors('File parsing failed.');
        }

        $processor = new XlsProcessor(storage_path('app/private').'/'.$path);
        $chunks = $processor->createChunks();

        InitChunks::dispatch(storage_path('app/private').'/'.$path)->onQueue('chunks');

        return back()->with('success', 'File uploaded successfully: ' . $path);

    }
}
