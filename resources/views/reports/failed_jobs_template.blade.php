<!doctype html>
<html lang="EN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>{{ config('app.name', 'Instant Messaging App') }}</title>
    <style>
      body{
        font-family: sans-serif;
        font-size:14px;
        max-width: 100%;
      }
      h1{
        text-transform:uppercase; 
        font-size:14px;
      }
      .smallprint{
        font-size:12px;
        font-weight: bold;
      }
      .table {
        width: 100%;
        max-width: 100%;
        margin-bottom: 1rem;
        color: #212529;
        border-bottom: 1px solid #dee2e6;
        font-size: 10px;
      }
      .table th,
      .table td {
        padding: 0.75rem;
        vertical-align: top;
        border-top: 1px solid #dee2e6;
        max-width:200px!important;
        text-align: left;
      }
      .table thead th {
        vertical-align: bottom;
      }
      .table td.longtext{
        word-wrap: break-word; 
        max-width:200px;
      }
      .table tbody + tbody {
        border-top: 2px solid #dee2e6;
      }
      .clearfix {
          overflow: auto;
          margin-bottom:20px;
      }
      .clearfix::after {
        content: "";
        clear: both;
        display: table;
      }
      .no-items{
        text-align: center;
      }
    </style>
</head>
<body>
    <div>
        <main>
            <h1>Report - Failed broadcasting Jobs</h1>
            <div class="clearfix">
                <div style="float:left">
                <span style="margin-right:15px" class="smallprint">From: {{$data['from']}}</span>
                <span class="smallprint">To: {{$data['to']}}</span>
                </div>
                <div style="float:right" class="smallprint">Total: {{$data['total']}}</div>
            </div>
            @if($data['total'] > 0)
            <table class="table" style="text-align:left">
                <thead>
                  <tr>
                    <th scope="col">Job</th>
                    <th scope="col">UUID</th>
                    <th scope="col">Failed At</th>
                    <th scope="col">Exception</th>
                    <th scope="col">Queue</th>
                    <th scope="col">Connection</th>
                  </tr>
                </thead>
                <tbody>
                @foreach($data['jobs'] as $job)
                  <tr>
                    <td>{{$job->name}}</td>
                    <td>{{$job->uuid}}</td>
                    <td style="min-width: 100px;max-width: 100px;">{{$job->failed_at}}</td>
                    <td class="longtext">{{$job->exception}}</td>
                    <td>{{$job->queue}}</td>
                    <td>{{$job->connection}}</td>
                  </tr>
                @endforeach
                </tbody>
              </table>
              @else
              <p class="no-items">There are no failed jobs for requested period.</p>
              @endif
        </main>
    </div>
</body>
</html>
