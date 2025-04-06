<x-app>
    <div class="row">
        <div class="col-6">
            <form action="{{route('xls.upload')}}" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                @csrf
                <label for="file">Select a file:</label>
                <input type="file" class="form-control" name="file" id="file" accept=".xls,.xlsx" required>
                <br>
                <x-input-error :messages="$errors->get('file')" class="mt-2" />
                <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
        <div class="col-6">
            <div class="alert" role="alert">
                <p>Last parsed row: <span id="parsed_row">0</span></p>
            </div>
        </div>
    </div>
    <script>

    </script>

</x-app>
