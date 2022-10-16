@extends('admin.layout.app')
@section('maincontent')
    <div class="d-flex justify-content-between mb-3">
        <div style="margin-bottom: 1.5rem; margin-top: 1rem;">
            <p style="margin: 0px;padding:0px;">Date:
                <span style="font-weight: bold;">{{ SessionController::date_reverse_full($date) }}</span>
            </p>
            <small>Update: {{ date('d-m-y H:i:s', strtotime($lastUpdate)) }}</small>
        </div>
        <div style="margin-bottom: 1.5rem; margin-top: 1rem;">
            <a href="{{ route('stockouts.index', ['date' => $date]) }}">
                <button class="btn btn-success btn-sm">Group</button>
            </a>
            <a href="{{ route('admin.stockout.list.all', ['date' => $date]) }}">
                <button class="btn btn-success btn-sm px-3">All</button>
            </a>
        </div>
    </div>

    <div class="clearfix">
        <table class="table table-striped" id="table_id">
            <thead>
                <tr>
                    <th scope="col">Product Name</th>
                    <th style="text-align:center;" scope="col">Quantity</th>
                    <th style="text-align:center;" scope="col">Details</th>
                    <th style="text-align:center;" scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->product->product_name }}</td>
                        <td style="text-align:center;">{{ number_format($product->quantity) }}</td>
                        <td style="text-align:left;">
                            {{ 'S=' . number_format($product->sell) }}<br>
                            {{ 'B=' . number_format($product->buy) }}<br>
                            {{ 'P=' . number_format($product->sell - $product->buy) }}
                        </td>
                        <td style="text-align: center;"><a
                                href="{{ route('stockouts.edit', [$product->product_id, 'date' => $date]) }}"
                                class="text-primary">Edit</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('extrascript')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#table_id').DataTable({
                "order": false,
                "lengthMenu": [
                    [-1],
                    ["All"]
                ]
            });
        });
    </script>
@endsection
