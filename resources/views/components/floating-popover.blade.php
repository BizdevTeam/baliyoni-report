<button id="popover-button" type="button" class="fixed bottom-8 right-6 text-white bg-red-700 hover:bg-red-800 font-medium text-sm text-center dark:bg-red-600 dark:hover:bg-red-700 rounded-full w-20 h-20 flex items-center justify-center">

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

<div id="popover-company-profile" class="absolute z-10 hidden opacity-0 transition-opacity duration-300">
    <div class="p-2 space-y-2 bg-transparent">
        <div class="flex flex-col items-end gap-2">
            <x-floating-export class="!bg-transparent !shadow-none hover:!bg-gray-100 dark:hover:!bg-gray-700" />
            <x-floating-button class="!bg-transparent !shadow-none hover:!bg-gray-100 dark:hover:!bg-gray-700" />
            <x-floating-ask class="!bg-transparent !shadow-none hover:!bg-gray-100 dark:hover:!bg-gray-700" />
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

