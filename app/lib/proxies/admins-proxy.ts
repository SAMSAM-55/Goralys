import {NextRequest} from "next/server";
import {roleGuard} from "@/app/lib/proxies/guard/role-guard";

export async function AdminsProxy(request: NextRequest) {
    return roleGuard(request, {allowedRoles: ['admin']});
}