<?php

namespace App\Http\Controllers;

use App\Jobs\InitChunks;
use App\Models\Row;
use App\Processors\XlsProcessor;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use function PHPUnit\Framework\isNull;

class UploadController extends Controller
{

    /**
     * @return Factory|Application|View
     */
    public function index(): Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View
    {
        return view('upload.index');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
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

        InitChunks::dispatch(storage_path('app/private').'/'.$path)->onQueue('chunks');

        return back()->with('success', 'File uploaded successfully: ' . $path);

    }
}
