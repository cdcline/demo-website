#!/bin/bash
themes=("default" "dev")
for theme in ${themes[@]}; do
  echo "Generating css for $theme"
  lessc less/$theme.less $theme.css
done

