function loadEditMembers(page = 1) {
    let rank = $("#member-rank :selected").val();
    $.getJSON(
        `/api/clans/members/${$('meta[name="clan_id"]').attr(
            "content"
        )}/${rank}/${page}/12`,
        (data) => {
            let html = "";
            for (let i in data.data) {
                let user = data.data[i].user;
                let ranks = ``;
                $("#member-rank option").each(function () {
                    if ($(this).val() < 100 || rank == 100) {
                        ranks += `<option value=${$(this).val()} ${
                            $(this).val() == rank ? "selected" : ""
                        }>${new Option($(this).text()).innerHTML}</option>`;
                    }
                });
                html += `<div class="edit-member inline center-text" style="position:relative;">
                        <a href="/user/${user.id}/">
                            <div class="clan-member ellipsis">
                                <img src="${BH.avatarImg(
                                    user.id
                                )}" style="width:115px;height:115px;">
                                <div class="ellipsis">${
                                    new Option(user.username).innerHTML
                                }</div>
                            </div>
                        </a>
                        ${
                            data.data[i].rank != 100
                                ? `<form method="POST" action="https://www.brick-hill.com/clan/edit">
                            <input type="hidden" name="_token" value="${$(
                                'meta[name="csrf-token"]'
                            ).attr("content")}">
                            <input type="hidden" name="type" value="kick_user">
                            <input type="hidden" name="clan_id" value="${$(
                                'meta[name="clan_id"]'
                            ).attr("content")}">
                            <input type="hidden" name="user_id" value="${
                                user.id
                            }">
                            <i class="fas fa-ban" style="position:absolute;top:12px;left:12px;cursor:pointer;" title="Kick" onclick="$(this).parent().submit()"></i>
                        </form>`
                                : ""
                        }
                        <div style="width:120px;padding-left:12px;">
                            <select class='select edit-member-select' name="value" ${
                                data.data[i].rank == 100 ? "disabled" : ""
                            } data-user="${user.id}">
                                ${ranks}
                            </select>
                        </div>
                    </div>`;
            }
            let pagehtml = "";
            for (let i of data.pages.pages) {
                pagehtml += `<a class="page${
                    i == page ? " active" : ""
                }" onclick="loadEditMembers(${i})">${i}</a>`;
            }
            $(".member-pages").html(pagehtml);
            $(".edit-holder").html(html);
        }
    );
}

function searchRelationClans(e) {
    if ($(e.target).hasClass("forum-create-button") || e.keyCode == 13) {
        let search = $("#clan-search-bar").val();

        if (search == "") return $(".relation-holder").html("");

        $.getJSON(
            `/api/clans/relations/${$('meta[name="clan_id"]').attr(
                "content"
            )}/${search}/`,
            (data) => {
                let html = "";
                if (data.data.length == 0)
                    return $(".relation-holder").html("No clans found");
                for (let i in data.data) {
                    let clan = data.data[i];
                    html += `<div class="clan-relation" style="padding:5px;position:relative;">
                            <a href="/clan/${clan.id}/">
                                <img src="${BH.storage_domain}/images/clans/${
                        clan.thumbnail
                    }.png" style="width:75px;height:75px;">
                                <span class="clan-name ellipsis">${
                                    new Option(clan.title).innerHTML
                                }</span>
                            </a>
                            <form method="POST" action="https://www.brick-hill.com/clan/edit" class="clan-form">
                                <input type="hidden" name="_token" value="${$(
                                    'meta[name="csrf-token"]'
                                ).attr("content")}">
                                <input type="hidden" name="type" value="new_relation">
                                <input type="hidden" name="clan_id" value="${$(
                                    'meta[name="clan_id"]'
                                ).attr("content")}">
                                <input type="hidden" name="to_id" value="${
                                    clan.id
                                }">
                                <input class="button small smaller-text mr1 green upload-submit" type="submit" value="ALLY" name="ally">
                                <input class="button small smaller-text red upload-submit" type="submit" value="ENEMY" name="enemy">
                            </form>
                        </div>`;
                }
                $(".relation-holder").html(html);
            }
        );
    }
}

window.loadEditMembers = loadEditMembers;
window.searchRelationClans = searchRelationClans;
