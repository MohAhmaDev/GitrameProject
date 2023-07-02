import React, { useEffect, useState } from 'react';
import axiosClient from '../axios-client';
import { Typography, Box, Button } from '@mui/material';
import NivoBar from './MUI/NivoBar';


const BarChart = () => {

    const [myData, setMyData] = useState({});
    const [mykey, setMykey] = useState();


    const getFinanceDashData = () => {
        axiosClient.get('/dash_finance_bar').then(({data}) => {
            setMyData(data.data);
            setMykey(data.column);
        }).catch((error) => {
            console.log(error)
        })
    }

    useEffect(() => {
        getFinanceDashData()
    }, [])

    console.log("mykey : ", mykey)
    console.log("column : ", myData)

    return (
        <div className="card animated fadeInDown">
            {((mykey && mykey.length !== 0) && (Object.keys(myData).length !== 0)) && <>
                <Typography
                    variant="h5"
                    fontWeight="600"
                    sx={{ padding: "10px 10px 0 30px" }}
                >
                    Realisation/Privision 
                </Typography>
                <Box height="300px" mt="-10px">
                    <NivoBar data={myData} columns={mykey} index={"Mois"} legend={"Chiffre d'affaire"}/>
                </Box>
            </>}
        </div>
    );
};

export default BarChart;