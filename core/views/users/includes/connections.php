<?php

/** @var SteamInfo\Models\Entities\Friends[] $friends */
$friends = $this->friends;

class Node
{
    public $name;
    public $picture;
    public $group;

    public function __construct($name, $picture, $group)
    {
        $this->name = $name;
        $this->picture = $picture;
        $this->group = $group;
    }
}

class Link
{
    public $source;
    public $target;
    public $value;

    public function __construct($source, $target, $value)
    {
        $this->source = $source;
        $this->target = $target;
        $this->value = $value;
    }
}

/**
 * @param SteamInfo\Models\Entities\Friends[] $friends
 * @return string JSON
 */
function getFriendsGraphJSON($friends)
{
    $nodes = array();
    $links = array();

    $user = $friends[0]->getUser();
    $name = str_replace("'", "", $user->getNickname());
    array_push($nodes, new Node($name, $user->getAvatarUrl(), 0));

    foreach ($friends as $iteration => $friend) {
        $name = str_replace("'", "", $friend->getFriend()->getNickname());
        if (strpos($name, "'")) continue; // TODO: FIX! (not working properly with some names)
        array_push($nodes, new Node($name, $friend->getFriend()->getAvatarUrl(), 1));
        array_push($links, new Link(($iteration + 1), 0, 4));

    }

    return json_encode(array(
        'nodes' => $nodes,
        'links' => $links
    ));
}

?>

<div id="graph"></div>

<script>
    var width = 940, height = 580;
    var color = d3.scale.category20();
    var force = d3.layout.force().charge(-600).linkDistance(50).size([width, height]);
    var svg = d3.select(document.getElementById("graph")).append("svg").attr("width", width).attr("height", height);
    var graph = JSON.parse('<?php echo getFriendsGraphJSON($friends); ?>');

    force.nodes(graph.nodes).links(graph.links).start()
        .linkStrength(0.3);

    var link = svg.selectAll(".link")
        .data(graph.links)
        .enter().append("line")
        .attr("class", "link")
        .style("stroke-width", function (d) {
            return Math.sqrt(d.value);
        });

    var node = svg.selectAll(".node")
        .data(graph.nodes)
        .enter().append("g")
        .attr("class", "node")
        .call(force.drag);

    node.append("rect")
        .attr("x", -15)
        .attr("y", -15)
        .attr("width", 30)
        .attr("height", 30)
        .style("fill", function(d) { return color(d.group); });

    node.append("image")
        .attr("xlink:href", function (d) {
            return d.picture;
        })
        .attr("x", -12)
        .attr("y", -12)
        .attr("width", 24)
        .attr("height", 24);

    node.append("title")
        .text(function (d) {
            return d.name;
        });

    force.on("tick", function () {
        link.attr("x1", function (d) {
            return d.source.x;
        })
            .attr("y1", function (d) {
                return d.source.y;
            })
            .attr("x2", function (d) {
                return d.target.x;
            })
            .attr("y2", function (d) {
                return d.target.y;
            });
        node.attr("transform", function (d) {
            return "translate(" + d.x + "," + d.y + ")";
        });
    });

</script>