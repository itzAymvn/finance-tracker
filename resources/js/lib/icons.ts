import {
    Briefcase,
    ShoppingCart,
    CreditCard,
    Utensils,
    Gamepad2,
    Music,
    Plane,
    Car,
    Home,
    Wifi,
    Gift,
    Heart,
    GraduationCap,
    Dumbbell,
    Receipt,
    PiggyBank,
    Building2,
    Server,
    Coffee,
    BookOpen,
    Smartphone,
    Banknote,
    Tag,
    type LucideIcon,
} from 'lucide-react';

export const CATEGORY_ICONS: Record<string, LucideIcon> = {
    Briefcase,
    ShoppingCart,
    CreditCard,
    Utensils,
    Gamepad2,
    Music,
    Plane,
    Car,
    Home,
    Wifi,
    Gift,
    Heart,
    GraduationCap,
    Dumbbell,
    Receipt,
    PiggyBank,
    Building2,
    Server,
    Coffee,
    BookOpen,
    Smartphone,
    Banknote,
    Tag,
};

export const CATEGORY_ICON_NAMES = Object.keys(CATEGORY_ICONS);

export function getCategoryIcon(name: string | null | undefined): LucideIcon {
    if (name && CATEGORY_ICONS[name]) {
        return CATEGORY_ICONS[name];
    }
    return Tag;
}
