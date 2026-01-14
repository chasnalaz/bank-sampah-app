<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Penjualan;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function riwayatTransaksi(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // 1. QUERY PENGELUARAN (Transaksi Nasabah)
        $queryTransaksi = Transaksi::with('nasabah')->latest();
        
        // 2. QUERY PEMASUKAN (Penjualan Tengkulak)
        $queryPenjualan = Penjualan::with('tengkulak')->latest();

        // Filter Tanggal untuk KEDUANYA
        if ($startDate && $endDate) {
            $queryTransaksi->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            $queryPenjualan->whereBetween('tanggal_jual', [$startDate, $endDate]);
        }

        $transaksi = $queryTransaksi->get(); // Data Pengeluaran
        $penjualan = $queryPenjualan->get(); // Data Pemasukan

        // 3. HITUNG TOTAL KEUANGAN
        $totalPengeluaran = $transaksi->sum('total_harga');
        $totalPemasukan = $penjualan->sum('total_pendapatan');
        $keuntungan = $totalPemasukan - $totalPengeluaran;

        return view('admin.laporan.transaksi', compact(
            'transaksi', 
            'penjualan', 
            'startDate', 
            'endDate', 
            'totalPengeluaran', 
            'totalPemasukan',
            'keuntungan'
        ));
    }

    public function cetakTransaksi(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // 1. QUERY PENGELUARAN (Transaksi Nasabah)
        $queryTransaksi = Transaksi::with('nasabah')->latest();
        
        // 2. QUERY PEMASUKAN (Penjualan Tengkulak)
        $queryPenjualan = Penjualan::with('tengkulak')->latest();

        // Filter Tanggal untuk KEDUANYA
        if ($startDate && $endDate) {
            $queryTransaksi->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            $queryPenjualan->whereBetween('tanggal_jual', [$startDate, $endDate]);
        }

        $transaksi = $queryTransaksi->get(); // Data Pengeluaran
        $penjualan = $queryPenjualan->get(); // Data Pemasukan

        // 3. HITUNG TOTAL KEUANGAN
        $totalPengeluaran = $transaksi->sum('total_harga');
        $totalPemasukan = $penjualan->sum('total_pendapatan');
        $keuntungan = $totalPemasukan - $totalPengeluaran;

        return view('admin.laporan.cetak_transaksi', compact(
            'transaksi', 
            'penjualan', 
            'startDate', 
            'endDate', 
            'totalPengeluaran', 
            'totalPemasukan',
            'keuntungan'
        ));
    }
}