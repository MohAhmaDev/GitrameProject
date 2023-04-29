import React from 'react';
import { Box } from '@mui/material';
import { DataGrid, GridToolbar } from '@mui/x-data-grid';

const Table = ({postData, postColumns}) => {




    return (
        <div>
            <Box m="20px">
                <Box m="40px 0 0 0" height="75vh" 
                sx={{
                    "& .MuiDataGrid-root": {
                        border: "none",
                        backgroundColor: "#0d47a1"
                    },
                    "& .MuiDataGrid-cell": {
                        borderBottom: "none",
                    },
                    "& .name-collumn--cell": {
                        color: "#00e676",
                    },
                    "& .MuiDataGrid-columnHeaders": {
                        backgroundColor: "#2196f3",
                        borderBottom: "none",
                    },
                    "& .MuiDataGrid-virtualScroller": {
                        backgroundColor: "#F8F8F8"
                    },
                    "& .MuiDataGrid-footerContainer": {
                        borderTop: "none",
                        backgroundColor: "#1976d2",
                    },
                    "& .MuiDataGrid-toolbarContainer .MuiButton-text": {
                        color: `#DCDCDC !important`
                    }
                }}>
                    <DataGrid 
                        rows={postData}
                        columns={postColumns}
                        components={{ Toolbar: GridToolbar }}
                    />
                </Box>
            </Box>
        </div>
    );
};

export default Table;