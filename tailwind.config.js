import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: "class",
    important: true, // Faz com que todas as classes Tailwind tenham !important
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./public/js/**/*.js",
    ],
    safelist: [
        // Classes do SPA Core
        "drawer",
        "drawer-content",
        "drawer-header",
        "drawer-body",
        "bottom-sheet",
        "sheet-content",
        "sheet-header",
        "sheet-body",
        "grabber-handle",
        "grabber-bar",
        "modal-overlay",
        "modal-dialog",
        "backdrop",
        "toast-item",
        "fab",
        "bottom-nav",
        "bottom-nav-inner",
        "bottom-nav-item",
        "page",
        "active",
        "open",
        "show",
        "bento-grid",
        "bento-widget",
        "app-header",
        "icon-btn",
        "list-item",
        "btn",
        "btn-primary",
        "btn-secondary",
        "btn-outline",
        "btn-ghost",
        "btn-block",
        "btn-lg",
        "input-field",
        "input-label",
        "card",
        "card-body",
        // Utilities importantes
        {
            pattern:
                /^(bg|text|border|ring)-(slate|rose|red|green|blue|purple|amber|emerald|indigo|pink|teal|cyan|lime|yellow|orange)-(50|100|200|300|400|500|600|700|800|900)$/,
        },
        {
            pattern:
                /^(rounded|p|px|py|pt|pb|pl|pr|m|mx|my|mt|mb|ml|mr|gap|space-x|space-y)-(0|0\.5|1|1\.5|2|2\.5|3|3\.5|4|5|6|7|8|10|12|14|16|20|24|32)$/,
        },
        {
            pattern:
                /^(w|h|min-w|min-h|max-w|max-h)-(0|1|2|3|4|5|6|7|8|10|11|12|14|16|20|24|32|40|44|48|56|64|72|80|96|full|screen|auto|fit|min|max)$/,
        },
        {
            pattern: /^(flex|grid|inline-flex|inline-grid)$/,
        },
        {
            pattern:
                /^(items|justify|content|self)-(start|end|center|between|around|evenly|stretch|baseline)$/,
        },
        {
            pattern:
                /^(text|font)-(xs|sm|base|lg|xl|2xl|3xl|4xl|5xl|thin|extralight|light|normal|medium|semibold|bold|extrabold|black)$/,
        },
        {
            pattern:
                /^(opacity|shadow|z)-(0|5|10|20|25|30|40|50|60|70|75|80|90|95|100|sm|md|lg|xl|2xl)$/,
        },
        {
            pattern:
                /^(transition|duration|ease)-(all|colors|opacity|shadow|transform|none|75|100|150|200|300|500|700|1000|linear|in|out|in-out)$/,
        },
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                background: "hsl(var(--background))",
                foreground: "hsl(var(--foreground))",
                primary: {
                    DEFAULT: "hsl(var(--primary))",
                    light: "hsl(var(--primary-light))",
                    dark: "hsl(var(--primary-dark))",
                    on: "hsl(var(--on-primary))",
                    container: "hsl(var(--primary-container))",
                    "container-on": "hsl(var(--on-primary-container))",
                },
                secondary: {
                    DEFAULT: "hsl(var(--secondary))",
                    light: "hsl(var(--secondary-light))",
                    dark: "hsl(var(--secondary-dark))",
                    on: "hsl(var(--on-secondary))",
                    container: "hsl(var(--secondary-container))",
                    "container-on": "hsl(var(--on-secondary-container))",
                },
                destructive: {
                    DEFAULT: "hsl(var(--destructive))",
                    on: "hsl(var(--on-destructive))",
                },
                card: {
                    DEFAULT: "hsl(var(--card))",
                    on: "hsl(var(--on-card))",
                },
                popover: {
                    DEFAULT: "hsl(var(--popover))",
                    on: "hsl(var(--on-popover))",
                },
                muted: {
                    DEFAULT: "hsl(var(--muted))",
                    on: "hsl(var(--on-muted))",
                },
                accent: {
                    DEFAULT: "hsl(var(--accent))",
                    on: "hsl(var(--on-accent))",
                },
                surface: {
                    DEFAULT: "hsl(var(--surface))",
                    on: "hsl(var(--on-surface))",
                },
                error: {
                    DEFAULT: "hsl(var(--error))",
                    on: "hsl(var(--on-error))",
                },
                success: {
                    DEFAULT: "hsl(var(--success))",
                    on: "hsl(var(--on-success))",
                },
                warning: {
                    DEFAULT: "hsl(var(--warning))",
                    on: "hsl(var(--on-warning))",
                },
                info: {
                    DEFAULT: "hsl(var(--info))",
                    on: "hsl(var(--on-info))",
                },
                input: {
                    DEFAULT: "hsl(var(--input))",
                    on: "hsl(var(--on-input))",
                    placeholder: "hsl(var(--input-placeholder))",
                    ring: "hsl(var(--ring))",
                    focus: "hsl(var(--input-ring-focus))",
                },
                button: {
                    primary: {
                        DEFAULT: "hsl(var(--button-primary))",
                        hover: "hsl(var(--button-primary-hover))",
                        focus: "hsl(var(--button-primary-focus))",
                        text: "hsl(var(--button-primary-text))",
                    },
                    secondary: {
                        DEFAULT: "hsl(var(--button-secondary))",
                        hover: "hsl(var(--button-secondary-hover))",
                        focus: "hsl(var(--button-secondary-focus))",
                        text: "hsl(var(--button-secondary-text))",
                    },
                    disabled: {
                        DEFAULT: "hsl(var(--button-disabled))",
                        text: "hsl(var(--button-disabled-text))",
                    },
                },
                ring: "hsl(var(--ring))",
                text: {
                    DEFAULT: "hsl(var(--text-primary))",
                    secondary: "hsl(var(--text-secondary))",
                    disabled: "hsl(var(--text-disabled))",
                    inverted: "hsl(var(--text-inverted))",
                },
                border: "hsl(var(--border))",
                radius: "var(--radius)",
                chart: {
                    1: "hsl(var(--chart-1))",
                    2: "hsl(var(--chart-2))",
                    3: "hsl(var(--chart-3))",
                    4: "hsl(var(--chart-4))",
                    5: "hsl(var(--chart-5))",
                },
                header: {
                    DEFAULT: "hsl(var(--header))",
                    on: "hsl(var(--on-header))",
                },
                group: {
                    1: "hsl(var(--group-1))",
                    onGroup1: "hsl(var(--on-group-1))",
                    2: "hsl(var(--group-2))",
                    onGroup2: "hsl(var(--on-group-2))",
                    3: "hsl(var(--group-3))",
                    onGroup3: "hsl(var(--on-group-3))",
                },
                syntax: {
                    background: "hsl(var(--syntax-background))",
                    foreground: "hsl(var(--syntax-foreground))",
                    comment: "hsl(var(--syntax-comment))",
                    string: "hsl(var(--syntax-string))",
                    keyword: "hsl(var(--syntax-keyword))",
                    identifier: "hsl(var(--syntax-identifier))",
                    literal: "hsl(var(--syntax-literal))",
                    number: "hsl(var(--syntax-number))",
                    operator: "hsl(var(--syntax-operator))",
                    punctuation: "hsl(var(--syntax-punctuation))",
                    function: "hsl(var(--syntax-function))",
                    variable: "hsl(var(--syntax-variable))",
                    type: "hsl(var(--syntax-type))",
                    class: "hsl(var(--syntax-class))",
                    attribute: "hsl(var(--syntax-attribute))",
                    property: "hsl(var(--syntax-property))",
                    boolean: "hsl(var(--syntax-boolean))",
                    constant: "hsl(var(--syntax-constant))",
                    invertedText: "hsl(var(--syntax-inverted-text))",
                },
            },
            borderRadius: {
                "2xl": "calc(var(--radius) + 4px)",
                xl: "calc(var(--radius) + 2px)",
                lg: "var(--radius)",
                md: "calc(var(--radius) - 2px)",
                sm: "calc(var(--radius) - 4px)",
            },

            keyframes: {
                fadeIn: {
                    "0%": { opacity: "0", transform: "translateY(20px)" },
                    "100%": { opacity: "1", transform: "translateY(0)" },
                },
                scaleIn: {
                    "0%": { transform: "scale(0.95)" },
                    "100%": { transform: "scale(1)" },
                },
            },
            animation: {
                fadeIn: "fadeIn 0.8s ease-out",
                scaleIn: "scaleIn 0.5s ease-in-out",
            },
        },
    },

    plugins: [forms],
};
