<x-app>

    @foreach($rows as $date)
        <div class="row">
            <div class="col-1">
                <p>{{\Carbon\Carbon::parse($date->first()->date)->format('d.m.Y')}}</p>
            </div>
            <div class="col-11">
                <table class="table table-responsive w-100 d-block d-md-table">
                    @foreach($date as $person)
                        <tr>
                            <td>
                                {{$person->row_id}}
                            </td>
                            <td>
                                {{$person->name}}
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    @endforeach

</x-app>
