<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    public function listStudent()
    {
        return response()->json(Student::all());
    }

    public function formStudent(Request $request, $action, ?Student $student)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'contact_number' => 'required|string|max:15'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => '404']);
        }

        $validatedData = $validator->validated();

        if ($action == 'create') {
            $student = new Student;

            $lastStudent = Student::latest('id')->first();
            $nextRegistrationNumber = $lastStudent ? intval($lastStudent->registration_number) + 1 : 1;
            $student->registration_number = str_pad($nextRegistrationNumber, 5, '0', STR_PAD_LEFT);
        }

        $student->first_name = $validatedData['first_name'];
        $student->last_name = $validatedData['last_name'];
        $student->address = $validatedData['address'];
        $student->contact_number = $validatedData['contact_number'];
        $student->save();

        return response()->json(['message' => 'Student save successfully']);
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return response()->json(['message' => 'Student deleted successfully']);
    }
}
