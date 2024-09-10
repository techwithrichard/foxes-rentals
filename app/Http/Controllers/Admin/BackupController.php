<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{

    public function index()
    {
        abort_unless(auth()->user()->can('view backup'), 403);
        //list all files in the storage/app/backup folder
        $backups = Storage::disk('backup')->files();

        return view('admin.backups.index', compact('backups'));

    }


    public function create()
    {
        abort_unless(auth()->user()->can('delete backup'), 403);
        $status = Artisan::call('backup:run');
        if ($status == 1) {
            return redirect()->back()->with('error', __('Backup failed.Try again later.'));

        } else {
            return redirect()->route('admin.backups.index')
                ->with('success', __('Backup created successfully'));

        }
    }


    public function store(Request $request)
    {
        // run the backup process


    }

    public function show($id)
    {
        abort_unless(auth()->user()->can('view backup'), 403);
        return Storage::disk('backup')->download($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        // remove the file from the storage
        Storage::disk('backup')->delete($id);

        return back()->with('success', __('Backup deleted successfully'));
    }
}
