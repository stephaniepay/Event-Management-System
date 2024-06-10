@extends('layout')

@section('title', 'Team Member Details')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">Team Member Details</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">ID</th>
                <th scope="col">Contribution (%)</th>
                <th scope="col">Phone Number</th>
                <th scope="col">Email</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Pay Eong Waon</td>
                <td>1181103284</td>
                <td>30%</td>
                <td>016-9776128</td>
                <td>1181103284@student.mmu.edu.my</td>
            </tr>
            <tr>
                <td>Pay Eong Zen</td>
                <td>1191103360</td>
                <td>30%</td>
                <td>016-9059485</td>
                <td>1191103360@student.mmu.edu.my</td>
            </tr>
            <tr>
                <td>Cheeng Tze Hang</td>
                <td>1201101378</td>
                <td>25%</td>
                <td>011-10788975</td>
                <td>1201101378@student.mmu.edu.my</td>
            </tr>
            <tr>
                <td>Hariyshwaran A/L Rameash</td>
                <td>1201102324</td>
                <td>15%</td>
                <td>011-61133969</td>
                <td>1201102324@student.mmu.edu.my</td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
