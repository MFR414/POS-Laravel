@extends('layouts.master')

@section('content')
<!-- Main content -->
<section class="content">
    <div class="main-container">
        @if(Session::has('success_message'))
            <div class="card card-success" style='margin-bottom: 20px'>
                <div class="card-header">
                    <div class="header-body">
                        <p class="title" style='color: white; margin: 0;'>Success</p>
                    </div>
                </div>
                <div class="card-body">
                    {{ Session::get('success_message') }}
                </div>
            </div>
        @endif

        @if(Session::has('error_message'))
            <div class="card card-danger" style='margin-bottom: 20px'>
                <div class="card-header">
                    <div class="header-body">
                        <p class="title" style='color: white; margin: 0;'>Error</p>
                    </div>
                </div>
                <div class="card-body">
                    {{ Session::get('error_message') }}
                </div>
            </div>
        @endif
        <br>
        <section>
            <div class="d-flex d-flex d-flex justify-content-between align-items-center p-2">
                <div class="title">
                    <h2>Laporan</h2>
                </div>
            </div>
        </section>
        <br>
        <section class="section">
            <div class="card">
                <div class="card-body" style="padding: 5px 10px;">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb" style="margin-bottom: 0px !important;">
                        <li class="breadcrumb-item"><a href="{{ route('application.dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Laporan</li>
                    </ol>
                </nav>
                </div>
            </div>
        </section>
        <br>
        <section class="section">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title-body">
                                <h3 class="title">Cari transaksi</h3>
                            </div>
                            <form action="{{ route('application.reports.index') }}" method="GET" style="margin-bottom: 0">
                                <section>
                                    <div class="row">
                                        <div class="col-sm-12 col-md-3">
                                            <div class="form-group" style='margin-bottom: 0'>
                                                <label class="control-label" for="transaction_date">Tanggal transaksi</label>
                                                <input type="date" class="form-control boxed" name="transaction_date" id="transaction_date" value="{{ $search_terms['transaction_date'] }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-3">
                                            <div class="form-group" style='margin-bottom: 0'>
                                                <label class="control-label">Tahun</label>
                                                <select class="form-control boxed" name="month" id="month" value="{{ $search_terms['transaction_month'] }}">
                                                    <option value="">Pilih Bulan</option>
                                                    <option value="1">Januari</option>
                                                    <option value="2">Februari</option>
                                                    <option value="3">Maret</option>
                                                    <option value="4">April</option>
                                                    <option value="5">Mei</option>
                                                    <option value="6">Juni</option>
                                                    <option value="7">Juli</option>
                                                    <option value="8">Agustus</option>
                                                    <option value="9">September</option>
                                                    <option value="10">Oktober</option>
                                                    <option value="11">November</option>
                                                    <option value="12">Desember</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-3">
                                            <div class="form-group" style='margin-bottom: 0'>
                                                <label class="control-label">Tahun</label>
                                                <select class="form-control boxed" name="year" id="year" value="{{ $search_terms['transaction_year'] }}">
                                                    <option value="">Pilih Tahun</option>
                                                    <option value="2024">2024</option>
                                                    <option value="2025">2025</option>
                                                    <option value="2026">2026</option>
                                                    <option value="2027">2027</option>
                                                    <option value="2028">2028</option>
                                                    <option value="2029">2029</option>
                                                    <option value="2030">2030</option>
                                                    <option value="2031">2031</option>
                                                    <option value="2032">2032</option>
                                                    <option value="2033">2033</option>
                                                    <option value="2034">2034</option>
                                                    <option value="2035">2035</option>
                                                    <option value="2036">2036</option>
                                                    <option value="2037">2037</option>
                                                    <option value="2038">2038</option>
                                                    <option value="2039">2039</option>
                                                    <option value="2040">2040</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-3">
                                            <button class="btn btn-primary" style="width: 100%;margin-top: 24px; height: 38px">Cari Transaksi</button>
                                        </div>
                                    </div>
                                </section>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <br>
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-striped table-responsive center" style="overflow-x: auto;">
                    <thead>
                        <tr>
                            <th style="width: 1%">
                                No
                            </th>
                            <th>
                                Nomor Transaksi
                            </th>
                            <th>
                                Tanggal Transaksi
                            </th>
                            <th>
                                Nama Pelanggan
                            </th>
                            <th>
                                Status Pembayaran
                            </th>
                            <th style="width: 20%">
                                Opsi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($transactions) <= 0)
                            <tr>
                                <td colspan="6">Tidak ada transaksi</td>
                            </tr>
                        @else
                            @foreach( $transactions as $index => $transaction )
                            <tr>
                                <td>
                                    {{$transactions->firstItem() + $index}}
                                </td>
                                <td>
                                    {{$transaction->transaction_number}}
                                </td>
                                <td>
                                    {{$transaction->formatted_transaction_date}}
                                </td>
                                <td>
                                    {{$transaction->customer_name}}
                                </td>
                                <td>
                                    @if($transaction->transaction_status == "Belum Dibayar")
                                        <span class="badge badge-danger">Belum Dibayar</span>
                                    @else    
                                        <span class="badge badge-success">Lunas</span>
                                    @endif
                                </td>
                                <td class="project-actions text-right" style="display: flex; gap:5px;">
                                    {{-- @if($transaction->transaction_status == "Belum Dibayar")
                                        <a class="btn btn-primary btn-sm" style="padding-top: 8px;" href="{{ route('application.transactions.payment.form', $transaction)}}">
                                            <i class="fas fa-money"></i> Payment Transaction
                                        </a>
                                    @else
                                        <a class="btn btn-info btn-sm" style="padding-top: 8px;" href="{{ route('application.invoices.check', $transaction)}}">
                                            <i class="fas fa-print"></i> generate invoice
                                        </a>
                                    @endif --}}
                                    {{-- <form method="POST" action="{{ route('application.users.admins.destroy', $transaction) }}">
                                        @csrf
                                        <input name="_method" type="hidden" value="DELETE">
                                        <button type="submit" class="btn btn-danger show_confirm" title='Delete'> <i class="fas fa-trash"> </i> Delete</button>
                                    </form> --}}
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- /.card -->

  </section>

  {{-- <script>
    $('.show_confirm').click(function(e) {
        if(confirm("Apakah anda yakin ingin menghapus data admin ini?")) {
            document.getElementById('deleteForm').submit();
        }
    });
  </script> --}}

@endsection
