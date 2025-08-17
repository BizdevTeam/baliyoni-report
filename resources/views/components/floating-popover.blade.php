<div class="relative group">
<!--Tooltips Tulisannya Export -->
<button id="popover-button" type="button" class="fixed bottom-8 right-6 text-white bg-red-700 hover:bg-red-800 font-medium text-sm text-center dark:bg-red-600 dark:hover:bg-red-700 rounded-full w-20 h-20 flex items-center justify-center">
    <div class="absolute right-full top-1/2 -translate-y-1/2 mr-3 hidden group-hover:block w-max">
      <span class="relative z-10 p-2 text-xs leading-none text-white whitespace-nowrap bg-black shadow-lg rounded-md">
        Menu
      </span>
      <!-- Segitiga arah ke tombol -->
      <div class="absolute top-1/2 -right-1 w-2 h-2 bg-black rotate-45 -translate-y-1/2"></div>
    </div>
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
        <g fill="none" stroke="currentColor" stroke-dasharray="16" stroke-dashoffset="16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
            <path d="M5 5h14">
                <animate fill="freeze" attributeName="stroke-dashoffset" dur="0.2s" values="16;0" />
            </path>
            <path d="M5 12h14">
                <animate fill="freeze" attributeName="stroke-dashoffset" begin="0.2s" dur="0.2s" values="16;0" />
            </path>
            <path d="M5 19h14">
                <animate fill="freeze" attributeName="stroke-dashoffset" begin="0.4s" dur="0.2s" values="16;0" />
            </path>
        </g>
    </svg>
</button>
</div>

{{-- <div id="popover-company-profile" class="absolute z-10 hidden opacity-0 transition-opacity duration-300">
    <div class="p-2 space-y-2 bg-transparent">
        <div class="flex flex-col items-end gap-2">
            <!--Tooltips Tulisannya Export -->
            <div class="relative group">
            <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 hidden group-hover:block w-max">
                        <span class="relative z-10 p-2 text-xs leading-none text-white whitespace-no-wrap bg-black shadow-lg rounded-md">Export All</span>
                        <div class="w-3 h-3 -mt-2 rotate-45 bg-black mx-auto"></div>
                    </div>
                <x-floating-export class="!bg-transparent !shadow-none hover:!bg-gray-100 dark:hover:!bg-gray-700" />
            </div>
            
            <!--Tooltips Tulisannya Filter By Range -->
            <div class="relative group">
            <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 hidden group-hover:block w-max">
                                    <span class="relative z-10 p-2 text-xs leading-none text-white whitespace-no-wrap bg-black shadow-lg rounded-md">Export All</span>
                                    <div class="w-3 h-3 -mt-2 rotate-45 bg-black mx-auto"></div>
                                </div>
            <x-floating-button class="!bg-transparent !shadow-none hover:!bg-gray-100 dark:hover:!bg-gray-700" />
            </div>
            <!--Tooltips Tulisannya Grid View -->
            <div class="relative group">
            <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 hidden group-hover:block w-max">
                    <span class="relative z-10 p-2 text-xs leading-none text-white whitespace-no-wrap bg-black shadow-lg rounded-md">Export All</span>
                    <div class="w-3 h-3 -mt-2 rotate-45 bg-black mx-auto"></div>
                </div>
            <x-floating-ask class="!bg-transparent !shadow-none hover:!bg-gray-100 dark:hover:!bg-gray-700" />
        </div>
    </div>
    <div data-popper-arrow></div>
</div> --}}
<div id="popover-company-profile" class="absolute z-10 hidden opacity-0 transition-opacity duration-300">
    <div class="p-2 space-y-2 bg-transparent">
        <div class="flex flex-col items-end gap-2">

            <!-- Tooltip Export -->
            <div class="group relative flex items-center">
                <!-- PERBAIKAN: Menggunakan 'invisible' dan 'opacity' untuk visibilitas yang lebih andal -->
                <div role="tooltip" class="absolute right-full top-1/2 z-10 mr-4 inline-block -translate-y-1/2 whitespace-nowrap rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 group-hover:opacity-100 dark:bg-gray-700 invisible group-hover:visible">
                    Export
                    <div class="absolute top-1/2 -right-1 h-2 w-2 -translate-y-1/2 rotate-45 bg-gray-900 dark:bg-gray-700"></div>
                </div>
                <x-floating-export class="!bg-transparent !shadow-none hover:!bg-gray-100 dark:hover:!bg-gray-700" />
            </div>
            
            <!-- Tooltip Filter By Range -->
            <div class="group relative flex items-center">
                <!-- PERBAIKAN: Menggunakan 'invisible' dan 'opacity' untuk visibilitas yang lebih andal -->
                <div role="tooltip" class="absolute right-full top-1/2 z-10 mr-4 inline-block -translate-y-1/2 whitespace-nowrap rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 group-hover:opacity-100 dark:bg-gray-700 invisible group-hover:visible">
                    Filter By Range
                    <div class="absolute top-1/2 -right-1 h-2 w-2 -translate-y-1/2 rotate-45 bg-gray-900 dark:bg-gray-700"></div>
                </div>
                <x-floating-button class="!bg-transparent !shadow-none hover:!bg-gray-100 dark:hover:!bg-gray-700" />
            </div>

            <!-- Tooltip Grid View -->
            <div class="group relative flex items-center">
                <!-- PERBAIKAN: Menggunakan 'invisible' dan 'opacity' untuk visibilitas yang lebih andal -->
                <div role="tooltip" class="absolute right-full top-1/2 z-10 mr-4 inline-block -translate-y-1/2 whitespace-nowrap rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 group-hover:opacity-100 dark:bg-gray-700 invisible group-hover:visible">
                    Grid View
                    <div class="absolute top-1/2 -right-1 h-2 w-2 -translate-y-1/2 rotate-45 bg-gray-900 dark:bg-gray-700"></div>
                </div>
                <x-floating-ask class="!bg-transparent !shadow-none hover:!bg-gray-100 dark:hover:!bg-gray-700" />
            </div>
        </div>
    </div>
    <div data-popper-arrow></div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let button = document.getElementById('popover-button');
        let popover = document.getElementById('popover-company-profile');

        button.addEventListener('click', function() {
            popover.classList.toggle('hidden'); // Toggle visibility
            popover.classList.toggle('opacity-0'); // Transition effect
        });
    });

</script>

