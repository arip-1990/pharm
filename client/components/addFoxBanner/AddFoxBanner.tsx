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
                window.yaContextCb.push(()=>{
                    window.Ya.adfoxCode.create({
                        ownerId: 5202103,
                        containerId: containerId,
                        params: {
                            p1: params.p1,
                            p2: params.p2
                        }
                    })
                })
                // dghxh относится к 120на80, dghwt это от dagfarm
            } else {
                console.error("Ya.adfoxCode не инициализирован!");
            }
        }
    }, [containerId, params]);
    return <div id={containerId}> </div>;
};

export default AdFoxBanner;



// <AdFoxBanner
//     containerId="adfox_174055308605049080"
//     params={{p1: "dghwt", p2: "ixfw" }}
// />