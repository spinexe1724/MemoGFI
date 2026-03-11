<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password | E - Memo Gratama</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-maroon-pattern {
            background-color: #800000;
            background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.05) 1px, transparent 0);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-900">

    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white rounded-[2.5rem] shadow-[0_32px_64px_-16px_rgba(0,0,0,0.1)] overflow-hidden border border-white p-8 md:p-12">
            
            <div class="text-center mb-8">
                <div class="inline-flex p-4 rounded-3xl bg-red-50 mb-6 text-[#800000]">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Lupa Kata Sandi?</h2>
                <p class="text-slate-500 mt-3 text-sm leading-relaxed">
                    Jangan khawatir! Masukkan email Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.
                </p>
            </div>

            @if (session('status'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-2xl text-xs text-green-700 font-bold">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-2xl text-xs text-red-600 font-bold">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
                @csrf
                <div class="space-y-1">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest ml-1">Email Terdaftar</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-6 py-4 bg-slate-50 border border-slate-200 text-slate-900 text-sm rounded-2xl focus:border-[#800000] focus:bg-white transition-all outline-none font-semibold" 
                        placeholder="nama@gratama.com">
                </div>

                <button type="submit" 
                    class="w-full py-4 px-6 text-white bg-[#800000] hover:bg-black font-extrabold rounded-2xl text-sm shadow-[0_20px_40px_-12px_rgba(128,0,0,0.3)] transition-all duration-500 active:scale-[0.98] tracking-widest uppercase">
                    Kirim Tautan Reset
                </button>

                <div class="text-center pt-2">
                    <a href="{{ route('login') }}" class="text-slate-400 text-sm font-bold hover:text-[#800000] transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Login
                    </a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>