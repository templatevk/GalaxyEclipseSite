<?php
//��������� ������
session_start();
$text=implode(" ", str_split($_SESSION['uid']));
//������� ����������� �� 3-� ��������� ��������
$im=ImageCreateFromJpeg(round(mt_rand(1,3)).".jpg");
//���� ����� �� 3-� ��������� ��������
$fo="addict".round(mt_rand(1,3)).".ttf";
//���������� ���� �������
$color=ImageColorAllocate($im,mt_rand(1,100),mt_rand(0,100),mt_rand(0,100));
//��������� �������, ��������� �����
ImageTtfText($im, 23, mt_rand(-3,3), 20, 22, $color, $fo,  $text);
//��������� ��� �����������
Header("Content-type: image/jpeg");
//������� � ������� �����������
ImageJpeg($im);
//��������� �����������
ImageDestroy($im);
?>