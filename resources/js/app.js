import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Import any SVG icons library you want to use
import { createIcons, Play, Users, FileText, GitBranchPlus, Building2, Briefcase, LineChart, LoaderCircle, FileCog, UserCircle, LogOut, LayoutDashboard, ShieldCheck } from 'lucide';

// Initialize icons when the DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    createIcons({
        icons: {
            Play,
            Users,
            FileText,
            GitBranchPlus,
            Building2,
            Briefcase,
            LineChart,
            LoaderCircle,
            FileCog,
            UserCircle,
            LogOut,
            LayoutDashboard,
            ShieldCheck
        }
    });
});