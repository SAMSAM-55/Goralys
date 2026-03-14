const SUBJECTS: [string, string][] = [
    ["apla",           "arts plastiques"],
    ["arts-plastiques","arts plastiques"],
    ["arts",           "arts plastiques"],
    ["ciav",           "cinéma audiovisuel"],
    ["cinema",         "cinéma audiovisuel"],
    ["cav",            "cinéma audiovisuel"],
    ["hggsp",          "histoire géographie géopolitique et sciences politiques"],
    ["hlphi",          "humanité littérature et philosophie"],
    ["hlp",            "humanité littérature et philosophie"],
    ["llce",           "langue littérature et culture etrangère"],
    ["llcer",          "langue littérature et culture etrangère"],
    ["maths",          "mathématiques"],
    ["math",           "mathématiques"],
    ["nsinf",          "numérique et sciences informatiques"],
    ["nsi",            "numérique et sciences informatiques"],
    ["phch",           "physique-chimie"],
    ["pc",             "physique-chimie"],
    ["ses",            "sciences economiques et sociales"],
    ["svt",            "sciences et vie de la terre"],
];

export function getLongFromShort(short: string): string {
    const search = short.toLowerCase().trim();
    const match = SUBJECTS.find(([key]) => key.startsWith(search));
    return match ? match[1] : search;
}