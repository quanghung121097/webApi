@extends('frontend.master')
@section('title')
	<title>Liên hệ</title>
@endsection
@section('content')
<div class="container">
	<h3 style="text-align: center;">Liên hệ</h3>
	<hr>
	<div class="row">
		<div class="col-sm-5">
			<div class="row mb-1">
				<h6 class="col-sm-4"><i class="fas fa-phone-alt"></i> Điện thoại</h6><div class="col-sm-8"> 0788337682</div>
			</div>
			<div class="row mb-1">
				<h6 class="col-sm-4"><i class="fas fa-envelope"></i> Email</h6><div class="col-sm-8"> quanghung121097@gmail.com</div>
			</div>
			<hr>
			<div class="card mb-3">
				<h5 class="card-header"><i class="fab fa-facebook" style="color: Dodgerblue"></i> Facebook</h5>
				<div class="card-body">
					 
				</div>
			</div>
			
		</div>
		<div class="col-sm-7">
			<div class="card">
			<h5 class="card-header"><i class="fas fa-map-marked-alt" style="color: red"></i> Bản đồ</h5>
				<div class="card-body">
	   	 		{{-- <iframe  src="" width="600" height="450" frameborder="0" style="border:none; width: 100%" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe> --}}
	   	 		<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3725.304433926158!2d105.80317401476239!3d20.980430386024562!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135acc27a9d8f07%3A0x8ae84b37dcad0702!2zTmfDtSAyIFTDom4gVHJp4buBdSwgVHJp4buBdSBLaMO6YywgVMOibiBUcmnhu4F1LCBUaGFuaCBUcsOsLCBIw6AgTuG7mWksIFZp4buHdCBOYW0!5e0!3m2!1svi!2sus!4v1639032598545!5m2!1svi!2sus" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
			</div>
   	 		</div>
    	</div>
    </div>
</div>
@endsection