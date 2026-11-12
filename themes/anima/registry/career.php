<?php
/**
 * Anima — Career page content registry (editable hero copy + team photo).
 * Job listings themselves (title, level, experience, responsibilities, requirements,
 * deadline, role, location) are managed under Admin → Career.
 */
return [
  'title'      => ['label'=>'Judul baris 1',      'type'=>'text', 'group'=>'Header','default'=>'Build the future with Us'],
  'title2'     => ['label'=>'Judul baris 2 (biru)','type'=>'text','group'=>'Header','default'=>'Grow your career at Sapta Tunas Teknologi'],
  'lead'       => ['label'=>'Paragraf intro',     'type'=>'html', 'group'=>'Header','default'=>'Be part of a team that delivers innovative technology solutions to the challenges of modern businesses. At Sapta Tunas Teknologi, you’ll work in a collaborative, learning-driven environment with real opportunities for personal and professional growth.'],
  'team_image' => ['label'=>'Foto tim (banner)',  'type'=>'image','group'=>'Header','default'=>'career/team.jpg'],
];
