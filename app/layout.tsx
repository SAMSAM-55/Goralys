import type { Metadata } from "next";
import "./globals.css";
import {SideNav} from "@/app/ui/nav/side-nav";
import {lusitana} from "@/app/lib/fonts";
import {ToastProvider} from "@/app/ui/toast/toast-provider";
import {ConfirmProvider} from "@/app/ui/modals/confirm/confirm-provider";
import React from "react";
import {AuthListener} from "@/app/lib/auth/auth-listener";
import FlashToastListener from "@/app/ui/toast/flash-toast-listener";
import {UserListener} from "@/app/lib/auth/user-listerner";
import {DraftModalProvider} from "@/app/ui/modals/drafts/draft-modal-provider";
import {ImportTopicsModalProvider} from "@/app/ui/modals/import-topics/import-topics-modal-provider";

export const metadata: Metadata = {
  title: "Goralys",
  description: "A cool project.",
};

export default function RootLayout({children}: Readonly<{children: React.ReactNode;}>) {
    return (
    <html lang="fr">
    <head>
        {/* Font Awesome CDN */}
        <link rel="stylesheet"
              href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
              integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
              crossOrigin="anonymous"
              referrerPolicy="no-referrer"
        />
        <title>Goralys</title>
    </head>

    <body className={`${lusitana.className} antialiased text-gray-900 bg-gray-50`}>

    <div id="toast-root"></div>
    <div id="confirm-root"></div>
    <div id="draft-modal-root"></div>
    <div id="import-topics-modal-root"></div>

    <ToastProvider>
        <ConfirmProvider>
            <DraftModalProvider>
                <ImportTopicsModalProvider>
                    <AuthListener />
                    <UserListener />
                    <FlashToastListener />
                    <div className="flex flex-row min-h-screen">
                        <SideNav />
                        {children}
                    </div>
                </ImportTopicsModalProvider>
            </DraftModalProvider>
        </ConfirmProvider>
    </ToastProvider>
    </body>
    </html>
  );
}
