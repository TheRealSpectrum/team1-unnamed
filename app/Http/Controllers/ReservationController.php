<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Reservation;
use App\Models\Table;

final class ReservationController extends Controller
{
    public function index()
    {
        $late = [];
        $active = [];
        $upcoming = [];
        $later = [];

        $tomorrow = new \DateTime("tomorrow");
        $now = new \DateTime("now");
        $plusOneHour = new \DateTime("+1hour");

        $data = Reservation::where("date_start", "<", $tomorrow)
            ->with("tables")
            ->orderBy("date_start")
            ->get();

        foreach ($data as $reservation) {
            if ($reservation->date_start > $plusOneHour) {
                array_push($later, $reservation);
                continue;
            }

            if ($reservation->date_start > $now) {
                array_push($upcoming, $reservation);
                continue;
            }

            if ($reservation->active) {
                array_push($active, $reservation);
                continue;
            }

            array_push($late, $reservation);
        }

        return view("reservations.index", [
            "reservations" => $data,
            "late" => $late,
            "active" => $active,
            "upcoming" => $upcoming,
            "later" => $later,
        ]);
    }

    public function create()
    {
        return view("reservations.create");
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            "name" => "required|string|between:2,255",
            "phone_number" => "required|string|regex:/^([0-9\s\-\+\(\)]*)$/",
            "guest_count" => "required|integer|min:1",
            "date" => "required|after_or_equal:today",
            "time" => "required",
            "event_type" => "string|nullable",
            "tables" => "",
            "notes" => "string|nullable",
        ]);

        $request["date_start"] = strtotime($request["date"] . $request["time"]);

        $request["date_end"] = $request["date_start"] + 60 * 60 * 3;
        $request["active"] = false;

        Reservation::create($request->all());

        return redirect("/reservation")->with(
            "success",
            "The reservation is set"
        );
    }

    public function edit()
    {
        $data = Reservation::all();
        $tables = Table::all();
        $pivot = [];
        foreach ($data as $reservation) {
            foreach ($reservation->tables as $table) {
                array_push($pivot, [
                    "reservation_id" => $reservation->id,
                    "table_id" => $table->id,
                ]);
            }
        }
        return view("reservations.edit", [
            "data" => $data,
            "tables" => $tables,
            "pivot" => $pivot,
        ]);
    }
}
