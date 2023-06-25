import React, { useEffect, useRef, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import 'table2excel';
import TableFinance from '../MUI/TableFinance';
import { Box, Button } from '@mui/material';

const Dashboard02 = ({hide = false}) => {
  const navigate = useNavigate();

  const exportData = () => {
    const Table2Excel = window.Table2Excel;
    var table2excel = new Table2Excel({
      exclude: '.noExl',
      defaultFileName: 'Worksheet Name',
      filename: 'SomeFile',
      fileext: '.xls',
      preserveColors: true
    });
    table2excel.export(document.querySelectorAll('table'));
  };


  return (
    <>
      <h1> Dashboard Finance </h1>
      <div className="card animated fadeInDown">
        <TableFinance/>
        {!hide && <Box sx={{ marginTop: '25px' }}>
            <Button style={{ marginLeft: '10px' }} onClick={() => navigate('/report/finances')} color="error">
                {' '}
                PDF REPORT{' '}
            </Button>
            <Button style={{ marginLeft: '10px' }} onClick={(ev) => {exportData()}} color="success">
                EXEL REPORT{' '}
            </Button>
        </Box>}
      </div>
    </>
  );
};

export default Dashboard02;

