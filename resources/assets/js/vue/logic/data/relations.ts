import AbstractData from "./abstract_data";

export default class ModelRelation extends AbstractData {
    static Item = new ModelRelation(1);
    static User = new ModelRelation(2);
    static Set = new ModelRelation(3);
    static ForumThread = new ModelRelation(4);
    static ForumPost = new ModelRelation(5);
    static Comment = new ModelRelation(6);
    static Message = new ModelRelation(7);
    static Clan = new ModelRelation(8);
    static SetPass = new ModelRelation(9);
    static Permission = new ModelRelation(10);
    static Outfit = new ModelRelation(11);
    static ItemVersion = new ModelRelation(12);
}
