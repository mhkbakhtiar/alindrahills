<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\ItemJurnal;
use App\Models\Jurnal;
use Illuminate\Http\Request;

class ItemJurnalController extends Controller
{
    /**
     * Get items by jurnal ID
     */
    public function getByJurnal($jurnalId)
    {
        $items = ItemJurnal::with(['perkiraan', 'kavling', 'user'])
            ->where('id_jurnal', $jurnalId)
            ->orderBy('urutan')
            ->get();

        return response()->json($items);
    }

    /**
     * Store new item (AJAX)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_jurnal' => 'required|exists:jurnal,id',
            'kode_perkiraan' => 'required|exists:perkiraan,kode_perkiraan',
            'keterangan' => 'nullable|string',
            'debet' => 'required|numeric|min:0',
            'kredit' => 'required|numeric|min:0',
            'kode_kavling' => 'nullable|exists:project_locations,kavling',
            'id_user' => 'nullable|exists:users,user_id',
        ]);

        if ($validated['debet'] > 0 && $validated['kredit'] > 0) {
            return response()->json([
                'error' => 'Debet dan Kredit tidak boleh diisi bersamaan'
            ], 422);
        }

        if ($validated['debet'] == 0 && $validated['kredit'] == 0) {
            return response()->json([
                'error' => 'Debet atau Kredit harus diisi'
            ], 422);
        }


        // Check jurnal status
        $jurnal = Jurnal::find($validated['id_jurnal']);
        if ($jurnal->status !== 'draft') {
            return response()->json(['error' => 'Cannot modify posted jurnal'], 403);
        }

        $item = ItemJurnal::create($validated);

        return response()->json([
            'success' => true,
            'item' => $item->load(['perkiraan', 'kavling', 'user'])
        ]);
    }

    /**
     * Update item (AJAX)
     */
    public function update(Request $request, $id)
    {
        $item = ItemJurnal::findOrFail($id);

        // Check jurnal status
        if ($item->jurnal->status !== 'draft') {
            return response()->json(['error' => 'Cannot modify posted jurnal'], 403);
        }

        $validated = $request->validate([
            'kode_perkiraan' => 'required|exists:perkiraan,kode_perkiraan',
            'keterangan' => 'nullable|string',
            'debet' => 'required|numeric|min:0',
            'kredit' => 'required|numeric|min:0',
            'kode_kavling' => 'nullable|exists:project_locations,kavling',
            'id_user' => 'nullable|exists:users,user_id',
        ]);

        if ($validated['debet'] > 0 && $validated['kredit'] > 0) {
            return response()->json([
                'error' => 'Debet dan Kredit tidak boleh diisi bersamaan'
            ], 422);
        }

        if ($validated['debet'] == 0 && $validated['kredit'] == 0) {
            return response()->json([
                'error' => 'Debet atau Kredit harus diisi'
            ], 422);
        }


        $item->update($validated);

        return response()->json([
            'success' => true,
            'item' => $item->load(['perkiraan', 'kavling', 'user'])
        ]);
    }

    /**
     * Delete item (AJAX)
     */
    public function destroy($id)
    {
        $item = ItemJurnal::findOrFail($id);

        // Check jurnal status
        if ($item->jurnal->status !== 'draft') {
            return response()->json(['error' => 'Cannot modify posted jurnal'], 403);
        }

        $item->delete();

        return response()->json(['success' => true]);
    }
}