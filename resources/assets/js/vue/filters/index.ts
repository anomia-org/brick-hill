/**
 * Filters as a function arent cached, this means it will be recomputed on every change of the component state
 * Never put computationally intensive functions here, use a computed value
 **/

export function filterTimeAgo(input: string | Date): string {
    const date = input instanceof Date ? input : new Date(input);
    const formatter = new Intl.RelativeTimeFormat("en");
    const ranges: { [key: string]: number } = {
        years: 3600 * 24 * 365,
        months: 3600 * 24 * 30,
        weeks: 3600 * 24 * 7,
        days: 3600 * 24,
        hours: 3600,
        minutes: 60,
        seconds: 1,
    };
    const secondsElapsed = (date.getTime() - Date.now()) / 1000;
    for (let key in ranges) {
        if (ranges[key as keyof typeof ranges] < Math.abs(secondsElapsed)) {
            const delta = secondsElapsed / ranges[key as keyof typeof ranges];
            return formatter.format(
                Math.round(delta),
                key as Intl.RelativeTimeFormatUnit
            );
        }
    }
    return "Never"; // idk what returns here
}

export function filterDate(date: string, options?: Intl.DateTimeFormatOptions) {
    return new Date(date).toLocaleDateString("en-gb", options);
}

export function filterDatetime(date: string) {
    let parsed = new Date(date);
    return `${parsed.toLocaleDateString("en-gb")} ${parsed.toLocaleTimeString(
        "en-us"
    )}`;
}

export function filterDatetimeLong(date: string) {
    const formatter = new Intl.DateTimeFormat("en-gb", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "numeric",
        minute: "numeric",
        hourCycle: "h12",
    });

    return formatter.format(new Date(date));
}

export function filterNumberCompact(number: number) {
    return new Intl.NumberFormat("en", { notation: "compact" }).format(number);
}

export function filterNumber(number: number) {
    return new Intl.NumberFormat("en").format(number);
}
