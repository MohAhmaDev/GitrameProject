import React from 'react';
import { Box, Typography, useTheme, colors } from '@mui/material';
import ProgressCircle from './ProgressCircle';

const StatBox = ({ title, subtitle, icon, progress, increase }) => {


    return (
        <Box width="90%" m="0 20px">
            <Box display="flex" justifyContent="space-between">
                <Box>
                    {icon}
                </Box>
                <Box>
                    <Typography variant='h5' fontWeight="bold" sx={{color: colors.grey[100]}}>
                        {title}
                    </Typography>
                </Box>
            </Box>
            <Box display="flex" justifyContent="space-between">
                <Typography varient='h4' sx={{color: "#fff"}}>
                    {subtitle}
                </Typography>
            </Box>
            
        </Box>
    );
};

export default StatBox;