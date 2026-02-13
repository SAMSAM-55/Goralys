import {cacheUserDataClient} from "@/app/lib/user/user.client";
import {emitUserEvent} from "@/app/lib/auth/user-event";

export class GoralysActionHandler {
    private onLogin = async () => {
        await cacheUserDataClient();
        emitUserEvent("login");
        return;
}
    public handle = async (r: Response) => {
        const data = await r.clone().json();

        if (!data || !data.action) { return; }

        const action = data.action;
        if (action === "login-success") { await this.onLogin(); }

        return;
    }
}