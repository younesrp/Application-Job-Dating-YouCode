<?php

namespace App\controllers\back;

use App\core\Controller;
use App\models\Student;

class StudentController extends Controller 
{
    public function index() 
    {
        $studentModel = new Student();
        $students = $studentModel->getAllStudents();

        $this->render('back/students/index', [
            'students' => $students
        ]);
    }
}