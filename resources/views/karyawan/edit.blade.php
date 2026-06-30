{{-- This view delegates to create.blade.php which handles both create and edit modes
     via isset($karyawan) checks. The shared form detects edit mode by the presence
     of the $karyawan variable and renders @method('PUT'), pre-filled fields, and
     inline salary components accordingly. --}}
@include('karyawan.create')
