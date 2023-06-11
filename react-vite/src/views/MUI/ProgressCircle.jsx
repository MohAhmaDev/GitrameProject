import { Box, useTheme, colors } from "@mui/material";

const ProgressCircle = ({ progress = "0.75", size = "40" }) => {
  const angle = progress * 360;
  return (
    <Box
      sx={{
        background: `radial-gradient(${colors.blue[400]} 55%, transparent 56%),
        conic-gradient(transparent 0deg ${angle}deg, ${colors.blueGrey[500]} ${angle}deg 360deg),
        ${"#fff"}`,
        borderRadius: "50%",
        width: `${size}px`,
        height: `${size}px`,
      }}
    />
  );
};

export default ProgressCircle;
