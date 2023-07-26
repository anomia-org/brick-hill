import AbstractData from "./abstract_data";

export default class ThumbnailType extends AbstractData {
    static AvatarFull = new ThumbnailType(1);
    static ItemFull = new ThumbnailType(2);
    static OutfitFull = new ThumbnailType(3);
    static ItemVersionFull = new ThumbnailType(4);
}
