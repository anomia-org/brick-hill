export default class AbstractData {
    private id;

    constructor(id: number) {
        this.id = id;
    }

    toString(): string {
        return this.id.toString();
    }

    toJSON(): number {
        return this.id;
    }
}
