<?php

namespace App\Http\Controllers;

use App\Models\VendorApplication;
use App\Models\Vendor;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class VendorApplicationController extends Controller
{
    public function __construct(protected WalletService $walletService) {}

    /** Form pendaftaran vendor */
    public function create(Request $request)
    {
        $user        = $request->user();
        $application = $user->vendorApplication;

        // Jika sudah ada aplikasi, redirect ke status
        if ($application) {
            return redirect()->route('become-vendor.status');
        }

        // Jika sudah vendor, tidak perlu daftar
        if ($user->isVendor()) {
            return redirect()->route('vendor.dashboard')->with('info', 'Anda sudah terdaftar sebagai vendor.');
        }

        return Inertia::render('BecomeVendor/Create');
    }

    /** Submit aplikasi vendor */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_name'    => 'required|string|max:255',
            'description'    => 'required|string|max:2000',
            'address'        => 'required|string|max:500',
            'city'           => 'required|string|max:100',
            'province'       => 'required|string|max:100',
            'phone'          => 'required|string|max:20',
            'email'          => 'required|email|max:255',
            'payment_method' => 'required|string|max:100',
            // FIX: Validasi MIME eksplisit (bukan hanya 'image') dan terima PDF.
            // Simpan di disk private agar tidak bisa diakses langsung via URL.
            'payment_proof'  => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $user = $request->user();

        if ($user->vendorApplication) {
            return back()->withErrors(['vendor_name' => 'Anda sudah memiliki aplikasi yang sedang diproses.']);
        }

        // FIX: Simpan di disk 'local' (private) bukan 'public'.
        // File bukti transfer adalah dokumen sensitif — tidak boleh
        // diakses langsung via URL publik.
        $proofPath = $request->file('payment_proof')->store('vendor-proofs', 'local');

        VendorApplication::create([
            'user_id'        => $user->id,
            'vendor_name'    => $validated['vendor_name'],
            'description'    => $validated['description'],
            'address'        => $validated['address'],
            'city'           => $validated['city'],
            'province'       => $validated['province'],
            'phone'          => $validated['phone'],
            'email'          => $validated['email'],
            'deposit_amount' => 10000000,
            'payment_method' => $validated['payment_method'],
            'payment_proof'  => $proofPath,
            'status'         => 'submitted',
        ]);

        return redirect()->route('become-vendor.status')
            ->with('success', 'Pendaftaran berhasil! Tim kami akan memverifikasi pembayaran deposit Anda.');
    }

    /** Status aplikasi */
    public function status(Request $request)
    {
        $user        = $request->user();
        $application = $user->vendorApplication;

        if (!$application) {
            return redirect()->route('become-vendor.create');
        }

        return Inertia::render('BecomeVendor/Status', [
            'application' => $application,
        ]);
    }
}
