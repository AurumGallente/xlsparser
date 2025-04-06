<x-app>
    <div class="row">
        <div class="col-6">
            <form action="{{route('xls.upload')}}" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                @csrf
                <label for="file">Select a file:</label>
                <input type="file" class="form-control" name="file" id="file" accept=".xls,.xlsx" required>
                <br>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
        <div class="col-6">
            <div class="alert" role="alert">
                <p>Last parsed row: <span id="parsed_row">0</span></p>
                <a class="btn btn-secondary" href="{{ route('table.index') }}">Table of Data</a>
            </div>
        </div>
    </div>
    <script>

    </script>

</x-app>
