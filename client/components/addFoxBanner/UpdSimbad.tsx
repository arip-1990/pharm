import { useEffect } from 'react'

interface Props {
    spotId: number
    className?: string
}

export const XoaltBanner = ({ spotId, className }: Props) => {
    useEffect(() => {
        if (typeof window !== 'undefined') {
            ;(window as any).adsbyxoalt = (window as any).adsbyxoalt || []
            ;(window as any).adsbyxoalt.push({})
        }
    }, [])

    return (
        <div
            className={`adsbyxoalt ${className ?? ''}`}
            style={{ width: '100%', height: '100%' }}
            xoalt-data-spot={spotId}
            xoalt-data-format="auto"
        />
    )
}
