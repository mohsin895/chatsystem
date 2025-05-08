// tailwind.config.js
/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
      ],
    theme: {
      extend: {
        colors: {
          customCyan: '#32c5d2',
          hoverCyan: '#23959f',
          purpoleCusHover: '#67317d',
          purpoleCus: '#8E44AD',
          deleteColor: '#faeaa9',
          borderColor: '#f3cc31',
          rightBarColor: '#364150',
          bodyColor: '#e9ecf3',
          cyneColor: '#c5ccd5',
          holybg: '#f0f6fa',
          holyColor: '#557386',
          holyHoverColor: '#e0eaf0',
          rightActiveColor: '#1caf9a',
          rightHoverColor: '#2C3542',
          loginFocus: '#ccc',
          employeebg: '#f7f7f7',
          tableColor: '#ECECEC',
          tableHeaderColor: '#E9ECEF',
          warningColor: '#eec00a',
          textColor: '#495057',
          profilebgColor: '#f7f7f7',
          bgDanger: '#ed6b75',
          bgPresent: '#5cb85c',
          bgDashboard1: '#578ebe',
          bgDashboard12: '#4884b8',
          bgDashboard2: '#e35b5a',
          bgDashboard21: '#e04a49',
          dashboardbg: '#fafafa',
          penColor: '#eec00a',
          statusSuccess: '#36c6d3',
          borderBottom: '#eef1f5',
        },
      },
      fontFamily: {
        sans: ['Graphik', 'sans-serif'],
        serif: ['Merriweather', 'serif'],
      },
    },
    variants: {
      extend: {
        borderColor: ['focus'],
      },
    },
    plugins: [],
  };
  