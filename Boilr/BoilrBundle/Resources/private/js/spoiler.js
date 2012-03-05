function spoilerToggle(event)
{
    var sliderID = "#spoiler-slider" + $(this).attr("slider-id");

    if($(sliderID).css("display") == "none") {
        $(this).text("[-]");
        $(this).attr("title", "Nascondi")
    } else {
        $(this).text("[+]");
        $(this).attr("title", "Mostra")
    }
    if (event.data.spoiler == "inline") {
        $(sliderID).toggle('fast');
    } else {
        $(sliderID).slideToggle('fast');
    }
}

function addSpoilers()
{
    $("div.spoiler-text").each(addBlockSpoiler);
    $(".block-spoiler-toggle").click({"spoiler": "block"}, spoilerToggle);
    $("span.spoiler-text").each(addInlineSpoiler);
    $(".inline-spoiler-toggle").click({"spoiler": "inline"}, spoilerToggle);
}

function addInlineSpoiler(i)
{
    $(this).attr("id", "spoiler-sliderI" + i);
    $("<span class='inline-spoiler-toggle' title='Mostra' slider-id='I" + i + "'>[+]</span>").insertBefore(this);
}

function addBlockSpoiler(i)
{
    $(this).attr("id", "spoiler-sliderB" + i);
    $("<div class='block-spoiler'>spoiler <span class='block-spoiler-toggle' title='Mostra' slider-id='B" + i + "'>[+]</span></div>").insertBefore(this);
}