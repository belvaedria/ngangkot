<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller {
    public function index() {
        $faqs = Faq::latest()->get();
        return view('admin.faq.index', compact('faqs'));
    }

    public function create() {
        return view('admin.faq.create');
    }

    public function store(Request $request) {
        $request->validate([
            'pertanyaan' => 'required',
            'jawaban' => 'required',
            'target' => 'required|in:penumpang,pengemudi,umum'
        ]);

        Faq::create($request->all());
        return redirect()->route('admin.faq.index')->with('success', 'FAQ ditambahkan');
    }

    public function edit(Faq $faq) {
        return view('admin.faq.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq) {
        $request->validate([
            'pertanyaan' => 'required',
            'jawaban' => 'required',
            'target' => 'required|in:penumpang,pengemudi,umum'
        ]);

        $faq->update($request->all());
        return redirect()->route('admin.faq.index')->with('success', 'FAQ diperbarui');
    }

    public function destroy(Faq $faq) {
        $faq->delete();
        return redirect()->route('admin.faq.index')->with('success', 'FAQ dihapus');
    }
}
