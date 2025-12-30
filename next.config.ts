import type { NextConfig } from "next";

const nextConfig: NextConfig = {
    async rewrites() {
        return [
            {
                source: "/api/:path*",
                destination: "http://localhost:80/API/:path*",
            },
        ];
    },
    reactStrictMode: false,
};

export default nextConfig;
