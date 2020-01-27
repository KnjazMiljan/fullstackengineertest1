<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/bootstrap.min.css">

        <title>Full Stack Engineer - Test</title>
    </head>
    <body>
    <div class="container pt-5">
        <h2>Full Stack Engineer - Test (Current Day: {{ $currentDay }})</h2>
        <p>Create a simple page to pull out shifts for current day, determine if that shift is related
            with any time clock and display them in simple HTML table. Use PHP (version 7+
            recommended), and Humanity API. You can use both of our API versions. Using
            frameworks/libraries is also acceptable, but not necessary.
        </p>

        @if(!empty($results) && count($results) > 0)
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Position​ ​(Schedule)</th>
                        <th>Shift</th>
                        <th>Timeclock</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($results as $result)
                        <tr>
                            <td>{{ $result['employee'] }}</td>
                            <td>{{ $result['scheduleName'] }}</td>
                            <td>{{ $result['shift'] }}</td>
                            <td>{{ $result['timeClock'] ? $result['timeClock'] : '/' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @elseif(!empty($error))
            <h3>Authentication error: {{ $error }}</h3>
        @else
            <h3>No Results found.</h3>
        @endif
    </div>
    </body>
</html>
