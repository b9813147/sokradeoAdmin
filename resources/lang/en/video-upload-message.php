<?php
return [
    'title'   => ':lecture_date New video notification :grade grade :subject :name (:teacher)',
    'content' => ":group_name  has a new video: \nLesson name: :name \nTeacher: :teacher (:habook)\nSubject: :subject \nGrade: :grade\nStudents: :student_count\nStudent feedback: :irs_count (:irsAvg per/person)\nLesson date: :lecture_date \n\nObservation data: \nObservervus: :observerCount person\nComments: :commentCount\n\nThe link of Sokrates lesson observation form :",
    'click'   => 'Browse',
];
