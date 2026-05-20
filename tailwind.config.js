/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                display: ['"Plus Jakarta Sans"', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            colors: {
                // Fresh lime-green brand to match the Fynix-style reference.
                brand: {
                    50:  '#f3fbe6',
                    100: '#e5f7c8',
                    200: '#cdee92',
                    300: '#b3e35c',
                    400: '#9bd844',   // primary accent (bright fresh green)
                    500: '#7fc028',
                    600: '#5fa31c',
                    700: '#447818',
                    800: '#2f5111',
                    900: '#1c3009',
                    950: '#0d1b03',
                },
                ink: {
                    50:  '#f7f8f5',
                    100: '#eef0eb',
                    200: '#dde0d8',
                    300: '#c2c7bc',
                    400: '#9ba294',
                    500: '#727a6c',
                    600: '#525a4d',
                    700: '#3d4439',
                    800: '#262a23',
                    900: '#15170f',  // near-black with a hint of green for contrast text
                },
                canvas: '#f4f6ef',    // app background (soft warm-green off-white)
            },
            borderRadius: {
                '2xl': '1.25rem',
                '3xl': '1.75rem',
            },
            boxShadow: {
                soft:  '0 6px 18px -8px rgba(20, 30, 10, 0.10), 0 2px 6px -2px rgba(20, 30, 10, 0.06)',
                pill:  '0 2px 6px -1px rgba(20, 30, 10, 0.10)',
                hero:  '0 20px 40px -20px rgba(95, 163, 28, 0.55)',
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
};
