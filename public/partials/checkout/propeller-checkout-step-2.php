<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Enum\AddressTypeCart;
use Propeller\PropellerHelper;

$delivery_address = $this->get_delivery_address();
?>
<svg style="display: none;">
    <symbol viewBox="0 0 18 21" id="shape-checkout-edit">
        <title><?php echo esc_html(__('Edit', 'propeller-ecommerce-v2')); ?></title>
        <g fill="none" fill-rule="evenodd">
            <path d="M17.34 1.978 16.023.659A2.242 2.242 0 0 0 14.432 0c-.577 0-1.152.22-1.592.659L.452 13.047l-.447 4.016a.844.844 0 0 0 .932.932l4.012-.444L17.341 5.16a2.25 2.25 0 0 0 0-3.182zM4.434 16.477l-3.27.362.363-3.275 9.278-9.277 2.91 2.91-9.281 9.28zM16.546 4.364l-2.037 2.037-2.91-2.91 2.037-2.037c.212-.212.495-.329.795-.329.3 0 .583.117.796.33l1.319 1.318a1.127 1.127 0 0 1 0 1.591z" fill="#005FAD" fill-rule="nonzero" />
            <path stroke="#005FAD" stroke-linecap="round" d="M.5 20.5h17" />
        </g>
    </symbol>
    <symbol viewBox="0 0 28 32" id="shape-calendar">
        <title><?php echo esc_html(__('Calendar', 'propeller-ecommerce-v2')); ?></title>
        <path d="M25 4h-3V.75a.752.752 0 0 0-.75-.75h-.5a.752.752 0 0 0-.75.75V4H8V.75A.752.752 0 0 0 7.25 0h-.5A.752.752 0 0 0 6 .75V4H3a3 3 0 0 0-3 3v22a3 3 0 0 0 3 3h22a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3zM3 6h22c.55 0 1 .45 1 1v3H2V7c0-.55.45-1 1-1zm22 24H3c-.55 0-1-.45-1-1V12h24v17c0 .55-.45 1-1 1zM9.25 20c.412 0 .75-.338.75-.75v-2.5a.752.752 0 0 0-.75-.75h-2.5a.752.752 0 0 0-.75.75v2.5c0 .412.338.75.75.75h2.5zm6 0c.412 0 .75-.338.75-.75v-2.5a.752.752 0 0 0-.75-.75h-2.5a.752.752 0 0 0-.75.75v2.5c0 .412.337.75.75.75h2.5zm6 0c.412 0 .75-.338.75-.75v-2.5a.752.752 0 0 0-.75-.75h-2.5a.752.752 0 0 0-.75.75v2.5c0 .412.337.75.75.75h2.5zm-6 6c.412 0 .75-.338.75-.75v-2.5a.752.752 0 0 0-.75-.75h-2.5a.752.752 0 0 0-.75.75v2.5c0 .412.337.75.75.75h2.5zm-6 0c.412 0 .75-.338.75-.75v-2.5a.752.752 0 0 0-.75-.75h-2.5a.752.752 0 0 0-.75.75v2.5c0 .412.338.75.75.75h2.5zm12 0c.412 0 .75-.338.75-.75v-2.5a.752.752 0 0 0-.75-.75h-2.5a.752.752 0 0 0-.75.75v2.5c0 .412.337.75.75.75h2.5z" />
    </symbol>
    <symbol viewBox="0 0 63 26" id="shape-dpd">
        <title>DPD</title>
        <g fill="none" fill-rule="evenodd">
            <path d="M40.926 20.859c-1.146.32-2.64.452-3.92.452-3.331 0-5.518-1.731-5.518-4.901 0-3.01 2.053-4.955 5.039-4.955.667 0 1.386.08 1.813.293V7.379h2.586v13.48zm-2.586-6.873c-.4-.187-.96-.293-1.6-.293-1.573 0-2.612.959-2.612 2.637 0 1.785 1.12 2.797 2.959 2.797.32 0 .826 0 1.253-.107v-5.034zm23.834 6.873c-1.146.32-2.64.452-3.946.452-3.306 0-5.518-1.731-5.518-4.901 0-3.01 2.052-4.955 5.038-4.955.694 0 1.387.08 1.813.293V7.379h2.613v13.48zm-2.613-6.873c-.4-.187-.933-.293-1.573-.293-1.6 0-2.639.959-2.639 2.637 0 1.785 1.146 2.797 2.986 2.797.32 0 .8 0 1.226-.107v-5.034zm-14.556 0c.426-.187 1.04-.24 1.546-.24 1.6 0 2.693.906 2.693 2.53 0 1.919-1.227 2.798-2.853 2.824v2.211h.134c3.359 0 5.358-1.838 5.358-5.114 0-2.984-2.133-4.742-5.278-4.742-1.6 0-3.173.373-4.213.8v12.653h2.613V13.986z" fill="#3F3F42" />
            <path fill="#DC0032" d="M22.584 5.648h.027zm-.64-.347.427.48.24-.133-.667-.347z" />
            <path fill="#DC0032" d="m21.304 4.955.827.959.293-.16-.32-.373-.8-.426z" />
            <path fill="#DC0032" d="m20.638 4.609 1.253 1.438.293-.16-.72-.852-.826-.426z" />
            <path d="m19.998 4.262 1.653 1.918.293-.16-1.146-1.331-.8-.427zm3.173 3.383-.187.107.187.213v-.32z" fill="#DC0032" />
            <path d="m19.358 3.916 2.053 2.398.293-.16-1.546-1.812-.8-.426zm3.706 3.783-.293.186.4.48v-.506l-.107-.16z" fill="#DC0032" />
            <path d="m18.692 3.57 2.48 2.877.319-.16-1.973-2.291-.826-.426zm4.132 4.262-.293.186.64.746v-.506l-.347-.426z" fill="#DC0032" />
            <path d="m18.052 3.223 2.906 3.357.293-.16-2.4-2.77-.8-.427zm4.532 4.769-.293.16.88 1.039v-.507l-.587-.692z" fill="#DC0032" />
            <path d="m17.412 2.877 3.306 3.836.293-.16-2.8-3.25-.799-.426zm4.932 5.248-.293.16 1.12 1.305v-.506l-.827-.959z" fill="#DC0032" />
            <path d="m16.746 2.53 3.732 4.316.293-.16-3.199-3.729-.826-.426zm5.358 5.728-.293.16 1.36 1.572v-.506l-1.067-1.226z" fill="#DC0032" />
            <path d="m16.106 2.184 4.132 4.822.293-.186-3.625-4.21-.8-.426zm5.758 6.207-.293.16 1.6 1.865V9.91l-1.307-1.52z" fill="#DC0032" />
            <path d="m15.44 1.838 4.558 5.301.293-.186-4.025-4.689-.827-.426zm6.211 6.687-.32.16 1.84 2.13v-.506l-1.52-1.784z" fill="#DC0032" />
            <path d="m14.8 1.492 4.958 5.78.32-.186-4.452-5.168-.827-.426zm6.611 7.166-.293.16 2.053 2.424v-.533l-1.76-2.051z" fill="#DC0032" />
            <path d="m14.16 1.145 5.385 6.26.293-.186-4.879-5.647-.8-.427zm7.011 7.646-.293.16 2.293 2.69v-.506l-2-2.344z" fill="#DC0032" />
            <path d="m13.493.773 5.812 6.766.293-.187-5.278-6.127-.827-.452zm7.438 8.151-.293.16 2.533 2.957v-.506l-2.24-2.61z" fill="#DC0032" />
            <path d="m12.853.426 6.212 7.246.293-.16L13.68.88l-.827-.453zm7.838 8.631-.293.16 2.773 3.25v-.533l-2.48-2.877z" fill="#DC0032" />
            <path d="m12.213.107 6.612 7.698.293-.16L13.013.533l-.8-.426zm8.238 9.084-.293.16 3.013 3.516v-.506l-2.72-3.17z" fill="#DC0032" />
            <path d="m11.787 0 6.798 7.939.293-.16L12.373.186l-.16-.08A.783.783 0 0 0 11.787 0zm8.424 9.324-.293.16 3.253 3.782v-.506l-2.96-3.436z" fill="#DC0032" />
            <path d="M11.787 0a1.34 1.34 0 0 0-.32.053l6.878 8.019.32-.16L11.867 0h-.08zm8.211 9.457-.293.16 3.466 4.076v-.533l-3.173-3.703z" fill="#DC0032" />
            <path d="M11.547.027c-.053.026-.133.053-.16.08l-.16.08 6.905 8.018.293-.16L11.547.027zm8.211 9.563-.293.187 3.706 4.315v-.506L19.758 9.59z" fill="#DC0032" />
            <path d="m11.28.133-.293.16 6.905 8.045.293-.16L11.28.133zm8.238 9.59-.293.187 3.946 4.582v-.506l-3.653-4.263z" fill="#DC0032" />
            <path d="m11.04.266-.293.16 6.905 8.045.293-.16L11.04.266zm8.238 9.59-.293.187 4.186 4.875v-.506l-3.893-4.555z" fill="#DC0032" />
            <path d="m10.8.4-.293.16 6.905 8.044.293-.16L10.8.4zm8.238 9.59-.293.186 4.426 5.142v-.507L19.038 9.99z" fill="#DC0032" />
            <path d="m10.56.533-.293.16 6.905 8.045.293-.16L10.561.533zm8.238 9.59-.293.186 4.666 5.408v-.506l-4.373-5.088z" fill="#DC0032" />
            <path d="m10.32.666-.293.16 6.905 8.045.293-.16L10.321.666zm8.265 9.59-.293.187 4.879 5.7v-.506l-4.586-5.38z" fill="#DC0032" />
            <path d="m10.08.773-.293.186 6.932 8.045.293-.16L10.081.773zm8.265 9.643-.293.16 5.119 5.967v-.506l-4.826-5.621z" fill="#DC0032" />
            <path d="m9.84.906-.293.16 6.932 8.071.293-.16L9.841.906zm8.265 9.643-.293.16 5.359 6.26v-.533l-5.066-5.887z" fill="#DC0032" />
            <path d="m9.6 1.039-.292.16 6.931 8.098.293-.186-6.931-8.072zm8.265 9.643-.293.16 5.599 6.527v-.506l-5.306-6.18z" fill="#DC0032" />
            <path d="m9.36 1.172-.292.16 6.931 8.098.293-.186-6.931-8.072zm8.265 9.644-.293.16 5.839 6.792v-.506l-5.546-6.446z" fill="#DC0032" />
            <path d="m9.12 1.305-.292.16 6.931 8.099.293-.187-6.931-8.072zm8.265 9.644-.293.16 6.079 7.086v-.533l-5.786-6.713z" fill="#DC0032" />
            <path d="m8.88 1.439-.292.16 6.931 8.098.294-.187L8.88 1.44zm8.292 9.643-.293.16 6.292 7.352v-.506l-5.999-7.006z" fill="#DC0032" />
            <path d="m8.641 1.545-.32.16 6.985 8.125.293-.187-6.958-8.098zm8.291 9.67-.293.16 6.532 7.619v-.506l-6.239-7.273z" fill="#DC0032" />
            <path d="m8.401 1.678-.32.16 7.012 8.152a.208.208 0 0 1 .106-.107l.16-.106-6.958-8.099zm8.291 9.67-.293.16 6.718 7.832a.777.777 0 0 0 .054-.24v-.213l-6.479-7.539z" fill="#DC0032" />
            <path d="m8.161 1.811-.32.16 7.172 8.338v-.106c0-.08.053-.187.106-.267L8.161 1.811zm8.291 9.67-.213.134c-.027 0-.053.026-.08.026l6.825 7.966a.9.9 0 0 0 .16-.32l-6.692-7.805z" fill="#DC0032" />
            <path d="m7.921 1.945-.32.16 7.412 8.63v-.532L7.92 1.945zm7.785 9.563 7.091 8.285c.08-.053.16-.16.24-.24l-6.825-7.938a.456.456 0 0 1-.373 0l-.133-.107z" fill="#DC0032" />
            <path d="m7.681 2.078-.32.16 15.196 17.688.214-.106.08-.08-6.985-8.125h-.027l-.613-.373c-.053-.027-.08-.08-.133-.133-.054-.08-.054-.134-.08-.187v-.293L7.68 2.078z" fill="#DC0032" />
            <path fill="#DC0032" d="m7.441 2.211-.32.16 15.196 17.688.294-.16z" />
            <path fill="#DC0032" d="m7.201 2.318-.32.16 15.223 17.715.293-.16z" />
            <path fill="#DC0032" d="m6.961 2.45-.32.16 15.223 17.716.293-.16z" />
            <path fill="#DC0032" d="m6.722 2.584-.32.16 15.222 17.715.294-.16z" />
            <path fill="#DC0032" d="m6.455 2.717-.293.16 15.222 17.742.294-.187z" />
            <path fill="#DC0032" d="m6.215 2.85-.293.16 15.222 17.742.294-.186z" />
            <path fill="#DC0032" d="m5.975 2.984-.293.16 15.25 17.741.292-.186z" />
            <path fill="#DC0032" d="m5.735 3.09-.293.16 15.25 17.768.292-.186z" />
            <path fill="#DC0032" d="m5.495 3.223-.293.16 15.25 17.769.293-.16z" />
            <path d="m5.255 3.357-.293.16 7.411 8.63.24.16c.107.054.214.187.214.32v.027l7.384 8.63.294-.159L5.255 3.357z" fill="#DC0032" />
            <path d="m5.015 3.49-.293.16 6.958 8.125.88.48L5.015 3.49zm7.785 9.057c0 .027.027.053.027.08v.453l7.144 8.338.294-.16-7.465-8.71z" fill="#DC0032" />
            <path d="m4.775 3.623-.293.16 6.505 7.592.88.48-7.092-8.232zm8.052 9.35v.506l6.931 8.072.293-.16-7.224-8.418z" fill="#DC0032" />
            <path d="m4.535 3.756-.293.16 6.079 7.06.853.479-6.639-7.699zm8.292 9.617v.533l6.691 7.778.293-.16-6.984-8.151z" fill="#DC0032" />
            <path d="m4.295 3.863-.293.16 5.625 6.553.854.506-6.186-7.22zm8.532 9.936v.506l6.451 7.513.294-.16-6.745-7.859z" fill="#DC0032" />
            <path d="m4.056 3.996-.294.16 5.172 6.02.853.506-5.731-6.686zm8.77 10.203v.506l6.212 7.246.294-.16-6.505-7.592z" fill="#DC0032" />
            <path d="m3.816 4.13-.294.159 4.72 5.514.852.48-5.278-6.154zm9.01 10.468v.533l5.972 6.98.294-.187-6.265-7.326z" fill="#DC0032" />
            <path d="m3.576 4.262-.294.16 4.266 4.982.853.48-4.825-5.622zm9.25 10.763v.506l5.76 6.713.292-.187-6.051-7.032z" fill="#DC0032" />
            <path d="m3.336 4.395-.294.16 3.813 4.45.88.479-4.4-5.089zm9.49 11.03v.505l5.52 6.447.292-.186-5.811-6.767z" fill="#DC0032" />
            <path d="m3.096 4.529-.293.16 3.359 3.915.88.507-3.946-4.582zm6.985 8.63.693.8v-.293c0-.053-.054-.133-.107-.16l-.586-.346zm2.746 2.665v.533l5.278 6.153.293-.186-5.571-6.5z" fill="#DB0032" />
            <path d="m2.856 4.635-.293.16 2.906 3.41.88.506-3.493-4.076zm6.531 8.125 1.387 1.599v-.507l-.507-.586-.88-.506zm3.44 3.49v.506l5.038 5.887.294-.16-5.332-6.233z" fill="#DC0032" />
            <path d="m2.616 4.768-.293.16 2.479 2.904.853.48-3.04-3.544zm6.078 7.62 2.08 2.397v-.506l-1.2-1.412-.88-.48zm4.133 4.262v.506l4.798 5.62.294-.16-5.092-5.966z" fill="#DC0032" />
            <path d="m2.376 4.902-.293.16 2.026 2.37.853.48-2.586-3.01zm5.652 7.086 2.746 3.196v-.506l-1.893-2.21-.853-.48zm4.799 5.088v.506l4.585 5.328.293-.16-4.878-5.674z" fill="#DC0032" />
            <path d="m2.136 5.035-.293.16 1.573 1.838.853.48-2.133-2.478zm5.199 6.553 3.439 3.996v-.506l-2.586-2.984-.853-.506zm5.492 5.887v.507l4.345 5.061.293-.16-4.638-5.408z" fill="#DC0032" />
            <path d="m1.896 5.168-.293.16 1.12 1.305.853.506-1.68-1.971zm4.746 6.02 4.132 4.822v-.506l-3.28-3.81-.852-.505zm6.185 6.687v.506l4.105 4.795.293-.16-4.398-5.141z" fill="#DC0032" />
            <path d="m1.656 5.301-.293.16.666.773.88.506L1.656 5.3zm4.292 5.515 4.826 5.594v-.506l-3.972-4.609-.854-.48zm6.879 7.485v.506l3.865 4.502.294-.16-4.16-4.848z" fill="#DC0032" />
            <path d="m1.416 5.408-.32.16.24.293.88.48-.8-.933zm3.84 5.008 5.518 6.42v-.533l-4.639-5.408-.88-.479zm7.57 8.285v.506l3.626 4.262.294-.186-3.92-4.582z" fill="#DC0032" />
            <path d="m1.176 5.541-.186.107.533.293-.347-.4zm3.386 4.475 6.212 7.22v-.506l-5.332-6.207-.88-.507zm8.265 9.084v.533l3.412 3.97.293-.187-3.705-4.316z" fill="#DC0032" />
            <path d="m3.896 9.617 6.878 8.018v-.506l-6.025-7.006-.853-.506zm8.93 9.91v.506L16 23.736l.293-.187-3.465-4.022z" fill="#DC0032" />
            <path d="m3.202 9.244 7.572 8.817v-.532L4.056 9.723l-.854-.48zm9.625 10.682v.506l2.932 3.437.293-.187-3.225-3.756z" fill="#DC0032" />
            <path d="m2.51 8.844 8.264 9.617v-.506L3.362 9.324l-.853-.48zm10.317 11.482v.533l2.692 3.143.294-.16-2.986-3.516z" fill="#DC0032" />
            <path d="m1.816 8.445 8.958 10.416v-.507L2.669 8.951l-.853-.506zm11.01 12.307v.506l2.453 2.877.294-.16-2.746-3.223z" fill="#DB0032" />
            <path d="m1.123 8.045 9.65 11.242v-.506L1.977 8.55l-.853-.506zm11.704 13.107v.506l2.239 2.61.293-.16-2.532-2.956z" fill="#DA0032" />
            <path d="m.43 7.672 10.344 12.014v-.506L1.31 8.152l-.88-.48zm12.397 13.88v.532l2 2.318.292-.16-2.292-2.69z" fill="#D90032" />
            <path d="M.403 7.645v.373l10.37 12.068v-.506L.617 7.752l-.213-.107zm12.424 14.332v.32a.203.203 0 0 1-.054.133l1.813 2.105.293-.16-2.052-2.398z" fill="#D80032" />
            <path d="M.403 7.912v.533l10.37 12.067v-.506L.404 7.912zM12.8 22.377c-.027.107-.107.213-.187.24l-.026.026 1.76 2.025.292-.16-1.84-2.131z" fill="#D70032" />
            <path d="M.403 8.338v.506l10.37 12.068v-.506L.404 8.338zM12.64 22.59c0 .027 0 .027-.027.027l-.266.16 1.76 2.024.293-.16-1.76-2.05z" fill="#D60032" />
            <path d="M.403 8.738v.506l10.37 12.067v-.506L.404 8.738zm12.024 13.985-.32.187 1.786 2.051.293-.186-1.76-2.052z" fill="#D50032" />
            <path d="M.403 9.164v.506l10.37 12.068v-.506L.404 9.164zm11.784 13.693-.187.106c-.053.027-.106.027-.16.027l1.813 2.104.293-.186-1.76-2.051z" fill="#D40032" />
            <path d="M.403 9.564v.506l10.37 12.067v-.506L.404 9.564zM11.36 22.857l2.053 2.37.293-.186-1.786-2.051c-.026 0-.08.026-.106.026h-.027a.385.385 0 0 1-.187-.053l-.24-.106z" fill="#D30032" />
            <path d="M.403 9.963v.533l12.77 14.865.293-.187-1.92-2.238-.586-.32a.37.37 0 0 1-.186-.319v-.266L.404 9.963z" fill="#D20032" />
            <path fill="#D10032" d="M.403 10.39v.505l12.53 14.599.293-.16z" />
            <path fill="#D00032" d="M.403 10.789v.506l12.29 14.332.294-.16z" />
            <path fill="#CF0032" d="M.403 11.189v.532l12.077 14.04.293-.16z" />
            <path fill="#CE0032" d="M.403 11.615v.506L12.24 25.893l.293-.16z" />
            <path d="M.403 12.014v.506L11.973 26l.24-.08.08-.053L.403 12.014z" fill="#CD0032" />
            <path d="M.403 12.414v.533L11.627 26h.16c.08 0 .16 0 .24-.027L.403 12.413z" fill="#CC0032" />
            <path d="M.403 12.84v.506l10.61 12.361.374.213c.08.053.213.08.32.08L.403 12.84z" fill="#CB0032" />
            <path fill="#CA0032" d="M.403 13.24v.506l9.918 11.561.88.507z" />
            <path fill="#C90032" d="M.403 13.666v.506l9.224 10.736.88.506z" />
            <path fill="#C80033" d="M.403 14.066v.506l8.531 9.936.853.506z" />
            <path fill="#C70033" d="M.403 14.465v.506l7.838 9.138.853.506z" />
            <path fill="#C60033" d="M.403 14.891v.507l7.118 8.284.88.507z" />
            <path fill="#C50033" d="M.403 15.291v.506l6.425 7.486.88.506z" />
            <path fill="#C40033" d="M.403 15.69v.507l5.732 6.686.88.506-6.612-7.698z" />
            <path fill="#C30033" d="M.403 16.117v.506l5.039 5.86.88.507z" />
            <path fill="#C20033" d="M.403 16.516v.507l4.346 5.061.88.506z" />
            <path fill="#C10033" d="M.403 16.916v.533l3.653 4.235.853.507z" />
            <path fill="#C00033" d="M.403 17.342v.506l2.933 3.437.88.506z" />
            <path fill="#BF0033" d="M.403 17.742v.506l2.24 2.637.88.48z" />
            <path fill="#BE0033" d="M.403 18.141v.533L1.95 20.46l.88.506z" />
            <path fill="#BD0033" d="M.403 18.568v.506l.853.985.88.507z" />
            <path d="M.403 18.967v.133c0 .267.187.586.4.72l.64.346-1.04-1.199z" fill="#BC0033" />
            <path d="M.563 19.58c.053.053.08.106.133.133l-.133-.133z" fill="#B03" />
        </g>
    </symbol>
</svg>
<div class="propeller-checkout-wrapper">
    <div class="container-fluid px-0 checkout-header-wrapper">
        <div class="row align-items-start">
            <div class="col-12 col-sm me-auto checkout-header">
                <h1><?php echo esc_html(__('Order', 'propeller-ecommerce-v2')); ?></h1>
            </div>
        </div>
    </div>
    <div class="container-fluid px-0">
        <div class="row">
            <div class="col-12 col-lg-8">
                <div class="checkout-wrapper-steps">
                    <div class="row align-items-start">
                        <div class="col-10 col-md-3">
                            <div class="checkout-step"><?php echo esc_html(__('Step 1', 'propeller-ecommerce-v2')); ?></div>
                            <div class="checkout-title"><?php echo esc_html(__('Your details', 'propeller-ecommerce-v2')); ?></div>
                        </div>
                        <div class="col-12 col-md-7 col-lg-6 ms-md-auto order-3 order-md-2 user-details">
                            <div class="user-fullname">Dhr. Christiaan Smeenk</div>
                            <div class="user-addr-details">
                                Propeller<br>
                                Pilotenstraat 43 bg<br>
                                1059 CH Amsterdam<br>
                                Netherlands
                            </div>
                        </div>
                        <!-- <div class="col-2 col-md-1 order-2 order-md-3 d-flex justify-content-end">
                            <div class="edit-checkout">
                                <a href="/checkout/">
                                    <svg class="icon icon-edit" aria-hidden="true">
                                        <use xlink:href="#shape-checkout-edit"></use>
                                    </svg>    
                                </a>
                            </div>
                        </div> -->
                    </div>

                </div>
                <div class="checkout-wrapper-steps">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <div class="checkout-step"><?php echo esc_html(__('Step 2', 'propeller-ecommerce-v2')); ?></div>
                            <div class="checkout-title"><?php echo esc_html(__('Delivery', 'propeller-ecommerce-v2')); ?></div>
                        </div>
                        <div class="col-6 d-flex justify-content-end">
                            <div class="checkout-step-nr">2/3</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <form name="checkout-delivery" class="form-handler checkout-form validate" method="post">
                                <input type="hidden" name="action" value="cart_update_address" />
                                <input type="hidden" name="step" value="<?php echo esc_attr($slug); ?>" />
                                <input type="hidden" name="next_step" value="3" />
                                <input type="hidden" name="type" value="<?php echo esc_attr(AddressTypeCart::DELIVERY); ?>" />
                                <input type="hidden" name="icp" value="N" />
                                <input type="hidden" name="phone" value="none-provided" />

                                <fieldset>
                                    <div class="row form-group">
                                        <div class="col-form-fields col-12">
                                            <div class="row g-3">
                                                <div class="col-12 form-group form-check">
                                                    <label class="btn-radio-checkbox form-check-label ">
                                                        <input type="radio" class="form-check-input" name="add_delivery_address" value="N" checked="checked"> <span><?php echo esc_html(__('Delivery address is the same as billing address', 'propeller-ecommerce-v2')); ?></span>
                                                    </label>
                                                </div>
                                                <div class="col-12 form-group form-check">
                                                    <label class="btn-radio-checkbox form-check-label ">
                                                        <input type="radio" class="form-check-input" name="add_delivery_address" value="Y"> <span><?php echo esc_html(__('Have it delivered to a different address', 'propeller-ecommerce-v2')); ?></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset class="new-delivery-address">
                                    <div class="row form-group">
                                        <div class="col-form-fields col-12">
                                            <div class="row g-3">
                                                <div class="col-12 col-md-8 form-group col-user-company">
                                                    <label class="form-label" for="field_company"><?php echo esc_html(__('Company', 'propeller-ecommerce-v2')); ?></label>
                                                    <input type="text" name="company" value="<?php echo esc_attr($delivery_address->company); ?>" placeholder="<?php echo esc_html(__('Company', 'propeller-ecommerce-v2')); ?>" class="form-control required" id="field_company">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-form-fields col-12">
                                            <div class="row g-3">
                                                <div class="col-auto form-group form-check">
                                                    <label class="btn-radio-checkbox form-check-label ">
                                                        <input type="radio" class="form-check-input" name="gender" value="M" <?php echo esc_attr((string) $delivery_address->gender == 'M' ? 'checked' : ''); ?>> <span><?php echo esc_html(__('Mr.', 'propeller-ecommerce-v2')); ?></span>
                                                    </label>
                                                </div>
                                                <div class="col-auto form-group form-check">
                                                    <label class="btn-radio-checkbox form-check-label ">
                                                        <input type="radio" class="form-check-input" name="gender" value="F" <?php echo esc_attr((string) $delivery_address->gender == 'F' ? 'checked' : ''); ?>> <span><?php echo esc_html(__('Ms.', 'propeller-ecommerce-v2')); ?></span>
                                                    </label>
                                                </div>
                                                <div class="col-auto form-group form-check">
                                                    <label class="btn-radio-checkbox form-check-label ">
                                                        <input type="radio" class="form-check-input" name="gender" value="U" <?php echo esc_attr((string) $delivery_address->gender == 'U' ? 'checked' : ''); ?>> <span><?php echo esc_html(__('Other', 'propeller-ecommerce-v2')); ?></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-form-fields col-12">
                                            <div class="row g-3">
                                                <div class="col-12 col-md form-group col-user-firstname">
                                                    <label class="form-label" for="field_fname"><?php echo esc_html(__('First name', 'propeller-ecommerce-v2')); ?>*</label>
                                                    <input type="text" name="firstName" value="<?php echo esc_attr($delivery_address->firstName); ?>" placeholder="<?php echo esc_html(__('First name', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="field_fname">
                                                </div>
                                                <div class="col-12 col-md form-group col-user-middlename">
                                                    <label class="form-label" for="field_mname"><?php echo esc_html(__('Insertion (optional)', 'propeller-ecommerce-v2')); ?></label>
                                                    <input type="text" name="middleName" value="<?php echo esc_attr($delivery_address->middleName); ?>" placeholder="<?php echo esc_html(__('Insertion (optional)', 'propeller-ecommerce-v2')); ?>" class="form-control" id="field_mname">
                                                </div>
                                                <div class="col-12 col-md form-group col-user-lastname">
                                                    <label class="form-label" for="field_lname"><?php echo esc_html(__('Last name', 'propeller-ecommerce-v2')); ?>*</label>
                                                    <input type="text" name="lastName" value="<?php echo esc_attr($delivery_address->lastName); ?>" placeholder="<?php echo esc_html(__('Last name', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="field_lname">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col-form-fields col-12">
                                            <div class="row g-3">
                                                <div class="col-12 col-md-8 form-group col-user-mail">
                                                    <label class="form-label" for="email_<?php echo esc_attr($delivery_address->id); ?>"><?php echo esc_html(__('E-mail address', 'propeller-ecommerce-v2')); ?>*</label>
                                                    <input type="email" name="email" pattern="[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$" value="<?php echo esc_attr($delivery_address->email); ?>" placeholder="<?php echo esc_html(__('E-mail address', 'propeller-ecommerce-v2')); ?>*" class="form-control required email" id="email_<?php echo esc_attr($delivery_address->id); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col-form-fields col-12">
                                            <div class="row g-3">
                                                <div class="col-12 col-md-8 form-group col-user-address">
                                                    <label class="form-label" for="field_address"><?php echo esc_html(__('Street', 'propeller-ecommerce-v2')); ?>*</label>
                                                    <input type="text" name="street" value="<?php echo esc_attr($delivery_address->street); ?>" placeholder="<?php echo esc_html(__('Street', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="field_address">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-form-fields col-12">
                                            <div class="row g-3">
                                                <div class="col-12 col-md-8 form-group col-user-address_add">
                                                    <label class="form-label" for="field_address_add"><?php echo esc_html(__('Apt, suite, unit, etc.(optional)', 'propeller-ecommerce-v2')); ?></label>
                                                    <input type="text" name="number" value="<?php echo esc_attr($delivery_address->number); ?>" placeholder="<?php echo esc_html(__('Apt, suite, unit, etc.(optional)', 'propeller-ecommerce-v2')); ?>" class="form-control" id="field_address_add">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-form-fields col-12">
                                            <div class="row g-3">
                                                <div class="col-12 col-md-8 form-group col-user-zipcode">
                                                    <label class="form-label" for="field_zipcode"><?php echo esc_html(__('Postal code', 'propeller-ecommerce-v2')); ?>*</label>
                                                    <input type="text" name="postalCode" value="<?php echo esc_attr($delivery_address->postalCode); ?>" placeholder="<?php echo esc_html(__('Postal code', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="field_zipcode">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-form-fields col-12">
                                            <div class="row g-3">
                                                <div class="col-12 col-md-8 form-group col-user-city">
                                                    <label class="form-label" for="field_city"><?php echo esc_html(__('City', 'propeller-ecommerce-v2')); ?>*</label>
                                                    <input type="text" name="city" value="<?php echo esc_attr($delivery_address->city); ?>" placeholder="<?php echo esc_html(__('City', 'propeller-ecommerce-v2')); ?>*" class="form-control required" id="field_city">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-form-fields col-12">
                                            <div class="row g-3">
                                                <div class="col-12 col-md-8 form-group col-user-country">
                                                    <label class="form-label" for="field_country"><?php echo esc_html(__('Country', 'propeller-ecommerce-v2')); ?>*</label>

                                                    <?php
                                                    $countries = propel_get_countries();
                                                    $selected = 'NL';

                                                    if (isset($delivery_address->country) && !empty($delivery_address->country))
                                                        $selected = $delivery_address->country;
                                                    ?>

                                                    <select id="field_country" name="country" class="form-control required">
                                                        <?php foreach ($countries as $code => $name) { ?>
                                                            <option value="<?php echo esc_attr($code); ?>" <?php echo esc_attr($code == $selected ? 'selected' : ''); ?>><?php echo esc_html($name); ?></option>
                                                        <?php } ?>
                                                    </select>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-form-fields col-12">
                                            <div class="row g-3">
                                                <div class="col-12 col-md-8">
                                                    <label class="form-check-label">
                                                        <input class="form-check-input" type="checkbox" name="save_delivery_address" id="newDeliveryAddress" value="Y" required="" title="<?php echo esc_attr(__('Save this delivery address', 'propeller-ecommerce-v2')); ?>" aria-required="true">
                                                        <span><?php echo esc_html(__('Save this delivery address', 'propeller-ecommerce-v2')); ?></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset>
                                    <legend class="checkout-header">
                                        <?php echo esc_html(__('Shipping method', 'propeller-ecommerce-v2')); ?>
                                    </legend>
                                    <div class="row form-group">
                                        <div class="col-form-fields col-12">
                                            <div class="row px-2 row g-3 form-check carriers">
                                                <?php foreach ($carriers as $carrier) { ?>
                                                    <div class="col-12 col-md-8">
                                                        <label class="form-check-label carrier">
                                                            <span class="row d-flex align-items-center">
                                                                <input type="radio" name="carrier_select" value="<?php echo esc_attr($carrier->name); ?>" title="Selecteer Verzendwijze." data-rule-required="true" required="required" aria-required="true" class="required">
                                                                <span class="carrier-name col-4 col-md-3">
                                                                    <?php echo esc_html($carrier->name); ?>
                                                                    <?php if (!empty($carrier->logo)) { ?>
                                                                        <img src="<?php echo esc_url($carrier->logo); ?>" class="carrier-logo">
                                                                    <?php } ?>
                                                                </span>
                                                                <span class="carrier-cost col-3"><?php echo esc_html(PropellerHelper::currency()); ?> <?php echo esc_html($carrier->price); ?></span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <fieldset>
                                    <legend class="checkout-header">
                                        <?php echo esc_html(__('Delivery date', 'propeller-ecommerce-v2')); ?>
                                    </legend>
                                    <div class="row form-group">
                                        <div class="col-form-fields col-12 col-md-8">
                                            <div class="row px-2 d-flex row g-3 form-check deliveries">
                                                <div class="col-6 col-sm-3 mb-4">
                                                    <label class="form-check-label delivery">
                                                        <span class="row d-flex align-items-center text-center">
                                                            <input type="radio" name="delivery_select" value="24 juli" title="Select delivery date" data-rule-required="true" required="required" aria-required="true" class="required">
                                                            <div class="delivery-day col-12">Zaterdag</div>
                                                            <div class="delivery-date col-12">24 juli</div>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-6 col-sm-3 mb-4">
                                                    <label class="form-check-label delivery">
                                                        <span class="row d-flex align-items-center text-center">
                                                            <input type="radio" name="delivery_select" value="26 juli" title="Select delivery date" data-rule-required="true" required="required" aria-required="true" class="required">
                                                            <div class="delivery-day col-12">Maandag</div>
                                                            <div class="delivery-date col-12">26 juli</div>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-6 col-sm-3 mb-4">
                                                    <label class="form-check-label delivery">
                                                        <span class="row d-flex align-items-center text-center">
                                                            <input type="radio" name="delivery_select" value="27 juli" title="Select delivery date" data-rule-required="true" required="required" aria-required="true" class="required">
                                                            <div class="delivery-day col-12">Dinsdag</div>
                                                            <div class="delivery-date col-12">27 juli</div>
                                                        </span>
                                                    </label>
                                                </div>
                                                <div class="col-6 col-sm-3 mb-4">
                                                    <label class="form-check-label delivery">
                                                        <span class="row d-flex align-items-center text-center justify-content-center">
                                                            <input type="radio" name="delivery_select" value="0" title="Select delivery date" data-rule-required="true" required="required" aria-required="true" class="required">
                                                            <svg class="icon icon-calendar" aria-hidden="true">
                                                                <use xlink:href="#shape-calendar"></use>
                                                            </svg>
                                                            <div class="d-none delivery-day col-12">Zaterdag</div>
                                                            <div class="d-none delivery-date col-12">24 juli</div>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                                <div class="row form-group form-group-submit">
                                    <div class="col-form-fields col-12">
                                        <div class="row g-3">
                                            <div class="col-12 col-md-8">
                                                <input type="submit" class="btn-proceed btn-green" value="<?php echo esc_html(__('Choose payment method', 'propeller-ecommerce-v2')); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="checkout-wrapper-steps">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <div class="checkout-step"><?php echo esc_html(__('Step 3', 'propeller-ecommerce-v2')); ?></div>
                            <div class="checkout-title"><?php echo esc_html(__('Payment method', 'propeller-ecommerce-v2')); ?></div>
                        </div>
                        <div class="col-6 d-flex justify-content-end">
                            <div class="checkout-step-nr">3/3</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">

                <?php include $this->partials_dir . '/cart/propeller-shopping-cart-totals.php' ?>
            </div>
        </div>
    </div>
    <!-- <div class="calendar-modal modal modal-fullscreen-sm-down fade" id="datePickerModal" tabindex="-1" role="dialog" aria-labelledby="datePickerModalContent">
        <div class="modal-dialog modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="datePickerModalContent"><?php echo esc_html(__('Choose a delivery date', 'propeller-ecommerce-v2')); ?></h6>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="calendar-wrapper" id="calendar-wrapper"></div>
                    <div id="editor"></div>
                </div>
            </div>
        </div>
    </div> -->

</div>

<?php include $this->partials_dir . '/other/propeller-toast.php' ?>
