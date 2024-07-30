@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">아바타 생성</h1>

    <form id="avatar-create-form" action="{{ route('avatar.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div id="step-1" class="step">
            <h2 class="text-2xl font-semibold mb-4">기본 정보</h2>
            <div class="mb-4">
                <label for="name" class="block mb-2">이름</label>
                <input type="text" id="name" name="name" class="w-full px-3 py-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label for="profile_intro" class="block mb-2">프로필 소개</label>
                <textarea id="profile_intro" name="profile_intro" class="w-full px-3 py-2 border rounded" required></textarea>
            </div>
            <div class="mb-4">
                <label for="profile_image" class="block mb-2">프로필 이미지</label>
                <input type="file" id="profile_image" name="profile_image" accept="image/*">
            </div>
            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_public" value="1" class="form-checkbox">
                    <span class="ml-2">공개</span>
                </label>
            </div>
        </div>

        <div id="step-2" class="step hidden">
            <h2 class="text-2xl font-semibold mb-4">카테고리 및 해시태그</h2>
            <div class="mb-4">
                <label for="categories" class="block mb-2">카테고리</label>
                <input type="text" id="categories" name="categories" class="w-full px-3 py-2 border rounded" placeholder="콤마로 구분">
            </div>
            <div class="mb-4">
                <label for="hashtags" class="block mb-2">해시태그</label>
                <input type="text" id="hashtags" name="hashtags" class="w-full px-3 py-2 border rounded" placeholder="콤마로 구분">
            </div>
        </div>

        <div id="step-3" class="step hidden">
            <h2 class="text-2xl font-semibold mb-4">첫 메시지</h2>
            <div class="mb-4">
                <label for="first_message" class="block mb-2">첫 메시지</label>
                <textarea id="first_message" name="first_message" class="w-full px-3 py-2 border rounded" required></textarea>
            </div>
        </div>

        <div id="step-4" class="step hidden">
            <h2 class="text-2xl font-semibold mb-4">프로필 상세</h2>
            <div class="mb-4">
                <label for="profile_details" class="block mb-2">프로필 상세</label>
                <textarea id="profile_details" name="profile_details" class="w-full px-3 py-2 border rounded" required></textarea>
            </div>
        </div>

        <div id="step-5" class="step hidden">
            <h2 class="text-2xl font-semibold mb-4">미세 조정 데이터</h2>
            <div class="mb-4">
                <label for="fine_tuning_data" class="block mb-2">미세 조정 데이터</label>
                <textarea id="fine_tuning_data" name="fine_tuning_data" class="w-full px-3 py-2 border rounded" required></textarea>
            </div>
        </div>

        <div class="mt-6 flex justify-between">
            <button type="button" id="prev-btn" class="bg-gray-500 text-white px-4 py-2 rounded hidden">이전</button>
            <button type="button" id="next-btn" class="bg-blue-500 text-white px-4 py-2 rounded">다음</button>
            <button type="submit" id="submit-btn" class="bg-green-500 text-white px-4 py-2 rounded hidden">생성 완료</button>
        </div>
    </form>
</div>

<script>
    const steps = document.querySelectorAll('.step');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const submitBtn = document.getElementById('submit-btn');
    let currentStep = 0;

    function showStep(step) {
        steps.forEach((s, index) => {
            if (index === step) {
                s.classList.remove('hidden');
            } else {
                s.classList.add('hidden');
            }
        });

        prevBtn.classList.toggle('hidden', step === 0);
        nextBtn.classList.toggle('hidden', step === steps.length - 1);
        submitBtn.classList.toggle('hidden', step !== steps.length - 1);
    }

    nextBtn.addEventListener('click', () => {
        if (currentStep < steps.length - 1) {
            currentStep++;
            showStep(currentStep);
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentStep > 0) {
            currentStep--;
            showStep(currentStep);
        }
    });

    showStep(currentStep);
</script>
@endsection