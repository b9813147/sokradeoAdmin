<?php
return [
    'title'   => ':lecture_date 频道新增课例数据通知 :grade年级 :subject :name (:teacher)',
    'content' => ":group_name  频道新增课例数据摘要:\n课例名称: :name \n授课教师: :teacher (:habook)\n课例学科: :subject \n年级: :grade\n学生人数: :student_count\n反馈次数: :irs_count (:irsAvg 次/人)\n授课日期: :lecture_date \n\n观议课数据:\n观课人数: :observerCount 人\n标记数: :commentCount\n\n苏格拉底观议课纪录表如下：",
    'click'   => '查看',
];
