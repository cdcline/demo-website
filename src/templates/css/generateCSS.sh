#!/bin/bash
themes=("orange" "black" "grey" "green")
for theme in ${themes[@]}; do
  echo "Generating css for theme: $theme"
  lessc less/$theme-theme.less $theme.css
done

