<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>HealthBillGuard</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-[#EFECE3] text-black min-h-screen flex flex-col">


    @include('partials.navbar')

    <main class="flex-1">
        @yield('content')
    </main>

    @include('partials.footer')

    <!-- Simple JS -->
    <script>
        function simulateScan() {
            document.getElementById('uploadBox').classList.add('hidden');
            document.getElementById('loadingState').classList.remove('hidden');

            setTimeout(() => {
                document.getElementById('loadingState').classList.add('hidden');
                document.getElementById('resultState').classList.remove('hidden');
            }, 2000);
        }

        function toggleDetail(id) {
            document.getElementById(id).classList.toggle('hidden');
        }
    </script>
</body>
</html>
