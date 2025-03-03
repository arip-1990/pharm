import React, { useEffect } from "react";

interface AdFoxBannerProps {
    containerId: string;
    params: {
        p1: string;
        p2: string;
    };
}

declare global {
    interface Window {
        yaContextCb: (() => void)[];
        Ya?: {
            adfoxCode: {
                create: (config: { ownerId: number; containerId: string; params: { p1: string; p2: string } }) => void;
            };
        };
    }
}


const AdFoxBanner: React.FC<AdFoxBannerProps> = ({ containerId, params }) => {
    useEffect(() => {
        if (typeof window !== "undefined" && window.yaContextCb) {
            console.log("AdFox banner init...");

            if (window.Ya && window.Ya.adfoxCode) {
                console.log("Calling Ya.adfoxCode.create...");
                window.Ya.adfoxCode.create({
                    ownerId: 5202103,
                    containerId: "adfox_174055308605049080",
                    params: { p1: "dghwt", p2: "ixfw" }
                });
            } else {
                console.error("Ya.adfoxCode не инициализирован!");
            }

        }
    }, [containerId, params]);

    return <div style={{ width: "300px", height: "250px" }} id="adfox_174055308605049080"> AdFox Placeholder </div>;
};

export default AdFoxBanner;



// <AdFoxBanner
//     containerId="adfox_174055308605049080"
//     params={{p1: "dghwt", p2: "ixfw" }}
// />