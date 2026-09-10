<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Http\Resources\ClassesResource;
use App\Http\Resources\StudentResource;
use App\Models\Classes;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $search = $request->search;
    $class_id = $request->class_id;

    $students = Student::with(['class', 'section']);

    $students = $this->applySearch(
        $students,
        $search,
        $class_id
    );

    $students = $students
        ->paginate(10)
        ->withQueryString();

    return Inertia('Students/Index', [
        'students' => StudentResource::collection($students),
        'classes' => Classes::all(),
        'search' => $search,
        'class_id' => $class_id,
    ]);
}

protected function applySearch($query, $search, $class_id)
{
    return $query
        ->when($search, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
            });
        })
        ->when($class_id, function ($query, $classId) {
            $query->where('class_id', $classId);
        });
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classes = ClassesResource::collection(Classes::all());

        return inertia('Students/Create', [
            'classes' => $classes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentRequest $request)
    {
        Student::create($request->validated());
        return redirect()->route('students.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(student $student)
    {
        $classes = ClassesResource::collection(Classes::all());
        return inertia('Students/Edit',['classes'=>$classes,
           'student'=>StudentResource::make($student),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, Student $student)
    {
        $student->update($request->validated());
        return redirect()->route('students.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index');
    }
}
