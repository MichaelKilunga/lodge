<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoomRequest;
use App\Models\Room;
use App\Models\RoomStatus;
use App\Models\Transaction;
use App\Models\Type;
use App\Repositories\Interface\ImageRepositoryInterface;
use App\Repositories\Interface\RoomRepositoryInterface;
use App\Repositories\Interface\RoomStatusRepositoryInterface;
use App\Repositories\Interface\TypeRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    public function __construct(
        private RoomRepositoryInterface $roomRepository,
        private TypeRepositoryInterface $typeRepository,
        private RoomStatusRepositoryInterface $roomStatusRepositoryInterface
    ) {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->roomRepository->getRoomsDatatable($request);
        }

        $types = $this->typeRepository->getTypeList($request);
        $roomStatuses = $this->roomStatusRepositoryInterface->getRoomStatusList($request);

        return view('room.index', [
            'types' => $types,
            'roomStatuses' => $roomStatuses,
        ]);
    }

    public function create()
    {
        $types = Type::all();
        $roomstatuses = RoomStatus::all();
        $view = view('room.create', [
            'types' => $types,
            'roomstatuses' => $roomstatuses,
        ])->render();

        return response()->json([
            'view' => $view,
        ]);
    }

    public function store(StoreRoomRequest $request, ImageRepositoryInterface $imageRepository)
    {
        $room = Room::create($request->all());

        $this->handleImageUploads($request, $room, $imageRepository);

        return response()->json([
            'message' => 'Room '.$room->number.' created',
        ]);
    }

    public function show(Room $room)
    {
        $customer = [];
        $transaction = Transaction::where([['check_in', '<=', Carbon::now()], ['check_out', '>=', Carbon::now()], ['room_id', $room->id]])->first();
        if (! empty($transaction)) {
            $customer = $transaction->customer;
        }

        return view('room.show', [
            'customer' => $customer,
            'room' => $room,
        ]);
    }

    public function edit(Room $room)
    {
        $types = Type::all();
        $roomstatuses = RoomStatus::all();
        $view = view('room.edit', [
            'room' => $room,
            'types' => $types,
            'roomstatuses' => $roomstatuses,
        ])->render();

        return response()->json([
            'view' => $view,
        ]);
    }

    public function update(Room $room, StoreRoomRequest $request, ImageRepositoryInterface $imageRepository)
    {
        $oldNumber = $room->number;
        $room->update($request->all());

        if ($oldNumber !== $room->number) {
            $oldPath = public_path('img/room/'.$oldNumber);
            $newPath = public_path('img/room/'.$room->number);
            if (is_dir($oldPath)) {
                rename($oldPath, $newPath);
            }
        }

        if ($request->boolean('replace_images') || $request->input('replace_images') == '1') {
            foreach ($room->image as $img) {
                $imgPath = public_path('img/room/'.$room->number.'/'.$img->url);
                if (file_exists($imgPath)) {
                    @unlink($imgPath);
                }
                $img->delete();
            }
        }

        $this->handleImageUploads($request, $room, $imageRepository);

        return response()->json([
            'message' => 'Room '.$room->number.' updated!',
        ]);
    }

    public function destroy(Room $room, ImageRepositoryInterface $imageRepository)
    {
        try {
            DB::transaction(function () use ($room) {
                DB::table('facility_room')->where('room_id', $room->id)->delete();
                $room->delete();
            });

            $path = 'img/room/'.$room->number;
            $path = public_path($path);

            if (is_dir($path)) {
                $imageRepository->destroy($path);
            }

            return response()->json([
                'message' => 'Room number '.$room->number.' deleted!',
            ]);
        } catch (\Exception $e) {
            $errorCode = isset($e->errorInfo[1]) ? $e->errorInfo[1] : $e->getCode();
            $errorMessage = 'Error Code: '.$errorCode;

            if ($errorCode == 1451 || (isset($e->errorInfo[0]) && $e->errorInfo[0] == '23000')) {
                $errorMessage = 'It is still linked to existing booking transactions.';
            }

            return response()->json([
                'message' => 'Room number '.$room->number.' cannot be deleted! '.$errorMessage,
            ], 500);
        }
    }

    private function handleImageUploads(Request $request, Room $room, ImageRepositoryInterface $imageRepository)
    {
        $files = [];
        foreach ($request->allFiles() as $uploaded) {
            if (is_array($uploaded)) {
                foreach ($uploaded as $file) {
                    $files[] = $file;
                }
            } else {
                $files[] = $uploaded;
            }
        }

        if (! empty($files)) {
            $path = public_path('img/room/'.$room->number);
            foreach ($files as $file) {
                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    if ($file->isValid()) {
                        $lastFileName = $imageRepository->uploadImage($path, $file);
                        \App\Models\Image::create([
                            'room_id' => $room->id,
                            'url' => $lastFileName,
                        ]);
                    } else {
                        \Illuminate\Support\Facades\Log::error('Room image upload failed for room '.$room->number.': '.$file->getErrorMessage());
                    }
                }
            }
        }
    }
}
