import React, { useEffect, useState } from 'react';
import axiosClient from '../axios-client';
import { CircularProgress } from '@mui/material';

const TemplateTest = () => {


    const [trancheAge, setTrancheAge] = useState({});
    const [table, setTable] = useState({});


    const getTrancheAge = () => {
        axiosClient.post('/dash_femployes').then(({data}) => {
            setTrancheAge(data.tranchAge);
            setTable(data.tab);
        }).catch((error) => {
            console.log(error);
        })
    }

    useEffect(() => {
        getTrancheAge()
    }, [])


    const getColumnAge = () => {
        const rows = [];
        trancheAge.forEach((element) => {
          const cells = [];
          // rows.push(
          // <tr key={element?.Tranche_Age} className="F_">
          //   <th className="F_" key={`header-${element?.Tranche_Age}`}> {element?.Tranche_Age} </th>
          // </tr>);
          for (let index = 1; index < 9; index++) {
            let field;
            if (Array.isArray(table)) {
              switch (index) {
                case 1:
                  field = table.find(
                    (item) =>
                      item?.age === element?.Tranche_Age &&
                      item?.contact === 'CDI' &&
                      item?.sexe === 'Femme' &&
                      item?.temps === 'Temps plein'
                  );
                  break;
                case 2:
                  field = table.find(
                    (item) =>
                      item?.age === element?.Tranche_Age &&
                      item?.contact === 'CDI' &&
                      item?.sexe === 'Femme' &&
                      item?.temps === 'Temps partiel'
                  );
                  break;
                case 3:
                  field = table.find(
                    (item) =>
                      item?.age === element?.Tranche_Age &&
                      item?.contact === 'CDI' &&
                      item?.sexe === 'Homme' &&
                      item?.temps === 'Temps plein'
                  );
                  break;
                case 4:
                  field = table.find(
                    (item) =>
                      item?.age === element?.Tranche_Age &&
                      item?.contact === 'CDI' &&
                      item?.sexe === 'Homme' &&
                      item?.temps === 'Temps partiel'
                  );
                  break;
                case 5:
                  field = table.find(
                    (item) =>
                      item?.age === element?.Tranche_Age &&
                      item?.contact === 'CDD' &&
                      item?.sexe === 'Femme' &&
                      item?.temps === 'Temps plein'
                  );
                  break;
                case 6:
                  field = table.find(
                    (item) =>
                      item?.age === element?.Tranche_Age &&
                      item?.contact === 'CDD' &&
                      item?.sexe === 'Femme' &&
                      item?.temps === 'Temps partiel'
                  );
                  break;
                case 7:
                  field = table.find(
                    (item) =>
                      item?.age === element?.Tranche_Age &&
                      item?.contact === 'CDD' &&
                      item?.sexe === 'Homme' &&
                      item?.temps === 'Temps plein'
                  );
                  break;
                case 8:
                  field = table.find(
                    (item) =>
                      item?.age === element?.Tranche_Age &&
                      item?.contact === 'CDD' &&
                      item?.sexe === 'Homme' &&
                      item?.temps === 'Temps partiel'
                  );
                  break;
                default:
                  break;
              }
            }
      
            cells.push(
              <td key={`cell-${index}-${element?.Tranche_Age}`} className="F_">
                {field ? field.nb_effectifs : '-'}
              </td>
            );
          }
          rows.push(
          <tr key={`row-${element?.tranchAge}`}>
            <th className="F_" key={`header-${element?.Tranche_Age}`}> {element?.Tranche_Age} </th>
            {cells}
          </tr>); 
        });
        return rows;
    };



    
    // const getTable = () => {
    //     const rows = [];
    //     trancheAge.map((element) => {
    //       const cells = [];
    //       for (let index = 1; index < 9; index++) {
    //         let field;
    //         if (Array.isArray(table)) {
    //           switch (index) {
    //             case 1:
    //               field = table.find(
    //                 (item) =>
    //                   item?.age === element?.Tranche_Age &&
    //                   item?.contact === 'CDI' &&
    //                   item?.sexe === 'Femme' &&
    //                   item?.temps === 'Temps plein'
    //               );
    //               break;
    //             case 2:
    //               field = table.find(
    //                 (item) =>
    //                   item?.age === element?.Tranche_Age &&
    //                   item?.contact === 'CDI' &&
    //                   item?.sexe === 'Femme' &&
    //                   item?.temps === 'Temps partiel'
    //               );
    //               break;
    //             case 3:
    //               field = table.find(
    //                 (item) =>
    //                   item?.age === element?.Tranche_Age &&
    //                   item?.contact === 'CDI' &&
    //                   item?.sexe === 'Homme' &&
    //                   item?.temps === 'Temps plein'
    //               );
    //               break;
    //             case 4:
    //               field = table.find(
    //                 (item) =>
    //                   item?.age === element?.Tranche_Age &&
    //                   item?.contact === 'CDI' &&
    //                   item?.sexe === 'Homme' &&
    //                   item?.temps === 'Temps partiel'
    //               );
    //               break;
    //             case 5:
    //               field = table.find(
    //                 (item) =>
    //                   item?.age === element?.Tranche_Age &&
    //                   item?.contact === 'CDD' &&
    //                   item?.sexe === 'Femme' &&
    //                   item?.temps === 'Temps plein'
    //               );
    //               break;
    //             case 6:
    //               field = table.find(
    //                 (item) =>
    //                   item?.age === element?.Tranche_Age &&
    //                   item?.contact === 'CDD' &&
    //                   item?.sexe === 'Femme' &&
    //                   item?.temps === 'Temps partiel'
    //               );
    //               break;
    //             case 7:
    //               field = table.find(
    //                 (item) =>
    //                   item?.age === element?.Tranche_Age &&
    //                   item?.contact === 'CDD' &&
    //                   item?.sexe === 'Homme' &&
    //                   item?.temps === 'Temps plein'
    //               );
    //               break;
    //             case 8:
    //               field = table.find(
    //                 (item) =>
    //                   item?.age === element?.Tranche_Age &&
    //                   item?.contact === 'CDD' &&
    //                   item?.sexe === 'Homme' &&
    //                   item?.temps === 'Temps partiel'
    //               );
    //               break;
    //             default:
    //               break;
    //           }
    //         }
      
    //         cells.push(
    //           <td key={`cell-${index}-${element?.Tranche_Age}`} className="F_">
    //             {field ? field.nb_effectifs : '-'}
    //           </td>
    //         );
    //       }
    //       rows.push(<tr key={`row-${element?.tranchAge}`}>{cells}</tr>);
    //     });
    //     // return <tr key={`row-${element?.Tranche_Age}`}>{cells}</tr>;
    //     return rows;
    //   };
      
    
    console.log(table);
    return (
        <>
            {(trancheAge && Object.keys(table).length !== 0) ? 
            <table className="ftable" id="ftable">
                    <thead>
                        <tr>
                            <th className="F_"></th>
                            {[...Array(8)].map((_, i) => (
                                <th key={`contact-${i}`} className="F_"> {i < 4 ? "CDI" : "CDD"} </th>
                            ))}
                        </tr>
                    </thead>
                    <thead>
                        <tr>
                            <th className="F_"></th>
                            {[...Array(8)].map((_, i) => (
                                <th key={`sexe-${i}`} className="F_"> 
                                    {[0,1,4,5].includes(i) ? "Femme" : "Homme"} 
                                </th>
                            ))}
                        </tr>
                    </thead>
                    <thead>
                        <tr>
                            <th className="F_"></th>
                            {[...Array(8)].map((_, i) => (
                                <th key={`temps-${i}`} className="F_">{i % 2 ? "Temps partiel" : 
                                "Temps Plain"}</th>
                            ))}
                        </tr>
                    </thead>
                    <tbody>
                        {getColumnAge()}  
                        {/* {getTable()}                   */}
                    </tbody>
            </table>: <CircularProgress disableShrink />}
        </>
    );
};

export default TemplateTest;