{{-- <h1>{{$subjudul}}</h1>
<h1>{{$user}}</h1> --}}

<table class="table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Price</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $product )

        <tr>
            <td >{{$product->name}}</td>
            <td>{{$product->description}}</td>
            <td>{{$product->price}}</td>
        </tr>

        @endforeach

    </tbody>
</table>
